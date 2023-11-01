<?php

use ApiPlatform\Core\Operation\PathSegmentNameGeneratorInterface;
use Behat\Behat\Hook\Scope\BeforeScenarioScope;
use Behat\Gherkin\Node\PyStringNode;
use Behat\Mink\Element\DocumentElement;
use Behat\MinkExtension\Context\MinkContext;
use Behatch\Context\BaseContext;
use Behatch\Context\RestContext;
use Behatch\HttpCall\HttpCallResultPool;
use Behatch\HttpCall\Request;
use Doctrine\ORM\EntityManagerInterface;
use PHPUnit\Framework\Assert;
use Symfony\Component\Routing\RouterInterface;
use Symfony\Component\HttpFoundation\File\UploadedFile;

/**
 * Allows using entities by user-defined uniqueIdentifiers to avoid hard-coding ids in tests.
 *
 * self::$map - array - holds objects indexed by uniqueIdentifier
 *
 * Smart requests: replace parameters of type <uniqueIdentifier:property> in body or url, found from $map
 */
final class EntityHelperContext extends BaseContext
{
    /** @var EntityManagerInterface */
    protected $em;

    /** @var Request */
    protected $request;

    /** @var PathSegmentNameGeneratorInterface */
    protected $urlGenerator;

    /** @var HttpCallResultPool */
    protected $httpCallResultPool;

    /** @var RouterInterface */
    protected $router;

    /** @var AuthContext */
    private $authContext;

    /** @var RestContext */
    private $restContext;

    /** @var MinkContext */
    private $minkContext;

    /** @var JsonContext */
    private $jsonContext;

    /** @var FileUploadContext */
    private $fileUploadContext;

    /** @var VirtualTicketingService */
    private $ticketingService;

    /**
     * @var array of array [ UniqueIdentifier => Object ]
     *            Holds objects across Scenarios
     */
    private static $map = [];

    public function __construct(
        EntityManagerInterface $em,
        PathSegmentNameGeneratorInterface $urlGenerator,
        RouterInterface $router,
        HttpCallResultPool $httpCallResultPool)
    {
        $this->em = $em;
        $this->urlGenerator = $urlGenerator;
        $this->router = $router;
        $this->httpCallResultPool = $httpCallResultPool;
    }

    /**
     * @BeforeScenario
     */
    public function loadRelatedContexts(BeforeScenarioScope $scope)
    {
        $this->authContext = $scope->getEnvironment()->getContext(AuthContext::class);
        $this->restContext = $scope->getEnvironment()->getContext(RestContext::class);
        $this->minkContext = $scope->getEnvironment()->getContext(MinkContext::class);
        $this->jsonContext = $scope->getEnvironment()->getContext(JsonContext::class);
        $this->fileUploadContext = $scope->getEnvironment()->getContext(FileUploadContext::class);
    }

    /**
     * Clear the objects map.
     */
    public function clearMap()
    {
        self::$map = [];
    }

    //
    // Smart Requests for Scenarios
    //

    /**
     * @Given I send a smart :method request to :url
     * @Given I send a smart :method request to :url with body:
     */
    public function iSendASmartRequest($method, $url, ?PyStringNode $body = null)
    {
        $url = $this->replaceSmartParameters($url);
        if ($body) {
            $body = $this->replaceSmartParameters($body);
        }
        $this->makeJSONRequest($method, $url, $body);
    }

    /**
     * @Given I smartly create the :entityType named :uniqueIdentifier with:
     */
    public function smartlyCreateEntityJSON($entityType, $uniqueIdentifier, PyStringNode $content)
    {
        $this->assertSmartEntityInMap($entityType, $uniqueIdentifier, false);

        return $this->makeSmartJSONRequest($entityType, $uniqueIdentifier, $content, 'POST');
    }

    /**
     * @Given I smartly update the :entityType named :uniqueIdentifier with:
     */
    public function smartlyUpdateEntityJSON($entityType, $uniqueIdentifier, PyStringNode $content)
    {
        return $this->makeSmartJSONRequest($entityType, $uniqueIdentifier, $content, 'PUT');
    }

    /**
     * @Given I smartly get the :entityType named :uniqueIdentifier
     */
    public function smartlyGetEntity($entityType, $uniqueIdentifier)
    {
        return $this->makeSmartJSONRequest($entityType, $uniqueIdentifier, null, 'GET');
    }

    /**
     * @When I login with this accessToken
     */
    public function loginWithAccessTokenFromLastRequest()
    {
        $response = $this->getLastResponseAsJson();
        $token = $response['accessToken'];
        $this->authContext->loginWithAccessToken($token, 'from-last-token');
    }

    //
    // Smart entities for SCENARIOS
    //

    /**
     * @Given there is a :entityType named :uniqueIdentifier
     * @Given there is a :entityType named :uniqueIdentifier with:
     */
    public function ensureEntityExistsOrCreateIt($entityType, $uniqueIdentifier, PyStringNode $content = null)
    {
        if (isset(self::$map[$uniqueIdentifier])) {
            $object = self::$map[$uniqueIdentifier];
            $this->assertObjectOfSmartType($object, $entityType, "$uniqueIdentifier is not of class $entityType");

            // TODO: should update Entity if already exists
            return $object;
        }

        $this->assertTrue(null !== $content, 'No content provided for entity creation');

        // TODO?: create through EntityManager->persist() ?
        $object = $this->createThroughPost($entityType, $content);
        self::$map[$uniqueIdentifier] = $object;

        return $object;
    }

    /**
     * @Then save the entity :entityType named :uniqueIdentifier
     *
     * Add entity to map, from last JSON
     *      (for entities created without smart helpers).
     */
    public function saveIt($entityType, $uniqueIdentifier)
    {
        $this->assertSmartEntityInMap($entityType, $uniqueIdentifier, false);
        self::$map[$uniqueIdentifier] = $this->retrieveObjectFromResponse($entityType);
    }

    /**
     * @Then update the entity :entityType named :uniqueIdentifier
     *
     * Update an entity stored in map, from last JSON (use when no smart was used)
     */
    public function updateIt($entityType, $uniqueIdentifier)
    {
        $this->assertSmartEntityInMap($entityType, $uniqueIdentifier, true);
        self::$map[$uniqueIdentifier] = $this->retrieveObjectFromResponse($entityType);
    }

    /**
     * @Then remove the entity :entityType named :uniqueIdentifier
     *
     * Remove an entity from map.
     */
    public function removeIt($entityType, $uniqueIdentifier)
    {
        if (!isset(self::$map[$uniqueIdentifier])) {
            throw new \LogicException("Entity with uniqueIdentifier $uniqueIdentifier does not exists");
        }
        unset(self::$map[$uniqueIdentifier]);
    }

    // sub/super set

    /**
     * @Then the JSON should be a smart superset of:
     */
    public function isSmartSuperSet(PyStringNode $content)
    {
        $expected = $this->replaceSmartParameters($content);
        $this->jsonContext->theJsonIsASupersetOf($expected);
    }

    /**
     * @Then the JSON should not be a smart superset of:
     *
     * @experimental
     */
    public function isNotSmartSuperSet(PyStringNode $content)
    {
        $expected = $this->replaceSmartParameters($content);
        $this->jsonContext->theJsonIsNotASupersetOf($expected);
    }

    /*****************
     * HELPERS
     ****************/

    //
    // SMART request/response utils
    //

    /**
     * Do a SMART request (GET/POST/PUT) for given EntityType,
     *      - replacing smart parameters (<UNIQUE_IDENTIFIER:ATTRIBUTE>) in body
     *      - update entity map if POST/PUT call succeeded.
     *
     * @param string $entityType
     * @param string $uniqueIdentifier
     * @param string $method
     *
     * @return array from JSON
     */
    private function makeSmartJSONRequest($entityType, $uniqueIdentifier, ?PyStringNode $content, $method)
    {
        $request = $this->makeJSONRequest($method,
            $this->generateUrl($entityType, $method, $uniqueIdentifier),
            $content ? $this->replaceSmartParameters($content->getRaw()) : null);
        $jsonResponse = $this->getJSONResponse($request);

        if (\in_array($method, ['POST', 'PUT']) && \in_array($this->getResponseStatus(), [200, 201])) {
            $object = $this->retrieveObjectFromResponse($entityType, $jsonResponse);
            self::$map[$uniqueIdentifier] = $object;
        }

        return $jsonResponse;
    }

    /**
     * Retrieve entity from database, based on a previous PUT/POST call (based on type & id in $data).
     *
     * @param string $entityType
     * @param array  $data       - should contain [ 'id' => * ]
     *
     * @return object
     */
    private function retrieveObjectFromResponse($entityType, $data = null)
    {
        $data = $data ?? $this->getLastResponseAsJson();

        /**
         * UserTransactions Entity is in sub directory.
         */
        $entityType = $this->resolveSmartType($entityType);

        $found = $this->em->getRepository($entityType)->find($data['id']);
        $this->assertTrue($found, "Object of type $entityType not found");

        return $found;
    }

    /**
     * Generate URL by Entity/Class name.
     *
     * @param $entityType
     * @param $method
     * @param $uniqueIdentifier
     *
     * @return string
     */
    private function generateUrl($entityType, $method, $uniqueIdentifier = null)
    {
        $segmentName = $this->urlGenerator->getSegmentName($entityType);
        switch ($method) {
            case 'GET':
            case 'PUT':
            case 'DELETE':
                $this->assertTrue(null !== $uniqueIdentifier);

                return $this->router->generate('api_'.$segmentName.'_'.strtolower($method).'_item', [
                    'id' => $this->getSmartEntityProperty($uniqueIdentifier, 'id'),
                ]);
            case 'POST':
                return $this->router->generate('api_'.$segmentName.'_post_collection');
            default:
                throw new LogicException("Method $method not yet implemented");
        }
    }

    //
    // Standard Request utils
    //

    /**
     * Create and send a JSON HTTP request.
     *
     * @param $method
     * @param $url
     * @param string $body
     *
     * @return DocumentElement
     */
    private function makeJSONRequest($method, $url, $body = null)
    {
        $this->restContext->iAddHeaderEqualTo('Content-Type', 'application/json');
        $this->restContext->iAddHeaderEqualTo('Accept', 'application/json');
        if ($this->authContext->getLastAuthBearer()) {
            $this->authContext->keepLoggedInForNextRequest();
        }
        return $this->restContext->iSendARequestTo($method, $url,
            $body instanceof PyStringNode ? $body : new PyStringNode([$body], 0),
            $this->fileUploadContext->attachments ?? []);
    }

    /**
     * Get the JSON content from a response made by makeJSONRequest.
     *
     * @return array
     */
    private function getJSONResponse(DocumentElement $request)
    {
        $content = $request->getContent();
        $content = json_decode($content, true);
        Assert::assertTrue(null !== $content && $content, 'Response is not JSON');

        return $content;
    }

    /**
     * Get last response, converted to an array.
     */
    private function getLastResponseAsJson()
    {
        return json_decode($this->httpCallResultPool->getResult()->getValue(), true);
    }

    /**
     * Get last response status code.
     *
     * @return int
     */
    private function getResponseStatus()
    {
        return $this->minkContext->getSession()->getStatusCode();
    }


    //
    // ENTITY CREATION utils
    //

    /**
     * Create a new entity, through POST
     *      TODO: (might need to be logged in as Admin?).
     *
     * @param $entityType
     * @param $data
     *
     * @return object
     */
    private function createThroughPost($entityType, $data)
    {
        $this->preSetupEntityCreation($entityType, $data);
        $data = $this->replaceSmartParameters($data);
        $req = $this->makeJSONRequest(
            'POST',
            $this->generateUrl($entityType, 'POST'),
            $data
        );
        $result = $this->getJSONResponse($req);

        return $this->retrieveObjectFromResponse($entityType, $result);
    }

    /**
     * Entity-specific pre-create Request setup.
     *
     * @param $entityType
     * @param $data
     */
    private function preSetupEntityCreation($entityType, $data)
    {
        $fullName = $this->resolveSmartType($entityType);
        switch ($fullName) {
            case \App\Entity\MediaObject::class:
                $this->fileUploadContext->thereAreDummyFilesToTest();
                $this->fileUploadContext->iAttachFilesToMyRequest('file');
                break;
            default:
                return;
        }
    }

//    private function createThroughManager($entityType, $data) {
//        $object = new ($$entityType)();
//
//        // TODO set data
//
//        $this->em->persist($object);
//        $this->em->flush();
//
//        return $object;
//    }

    //
    // SMART parsing utils
    //

    /**
     * Retrieve a smart object from map or predefined constants.
     *
     * @param $uniqueIdentifier
     * @param bool $mustExist
     *
     * @return \App\Entity\Users|mixed|null
     */
    private function getSmartEntity($uniqueIdentifier, $mustExist = true)
    {
        switch ($uniqueIdentifier) {
            case 'CURRENT_USER':
                return $this->authContext->getLoggedInUser();
            case 'NORMAL_USER':
                return $this->authContext->normalUser;
            case 'FACEBOOK_USER':
                return $this->authContext->getFacebookUser();
            case 'FREE_TICKET':
                return $this->ticketingService->getFreeTicket();
            case 'ADMIN_USER':
                return $this->authContext->adminUser;
            default:
                $this->assertSmartEntityInMap(null, $uniqueIdentifier, $mustExist);

                return self::$map[$uniqueIdentifier] ?? null;
        }
    }

    /**
     * Retrieve an entity's property, for the given identifier
     *      try to use getters (getProperty, isProperty) or the property name itself (must pe public).
     *
     * @param $uniqueIdentifier
     * @param $property
     *
     * @return string|mixed|null
     */
    private function getSmartEntityProperty($uniqueIdentifier, $property)
    {
        $object = $this->getSmartEntity($uniqueIdentifier, true);

        if (method_exists($object, "get$property")) {
            return \call_user_func([$object, "get$property"]);
        }
        if (method_exists($object, "is$property")) {
            return \call_user_func([$object, "is$property"]);
        }
        if (property_exists($object, $property)) {
            return $object->$property;
        }

        throw new \LogicException("Entity with uniqueIdentifier $uniqueIdentifier has no method (`get$property` or `is$property`) or public property `$property``");
    }

    /**
     * Replace smart parameters like <UNIQUE_IDENTIFIER:property> in the given string
     *      special identifiers: CURRENT_USER (references the current logged-in user).
     *
     * @param string|PyStringNode $string
     *
     * @return string|PyStringNode
     */
    public function replaceSmartParameters($string)
    {
        $pyString = false;
        if ($string instanceof PyStringNode) {
            $pyString = $string;
            $string = $string->getRaw();
        }

        $matches = [];
        while (preg_match('/<(\w+):(\w+)>/', $string, $matches)) {
            $matches[1];
            $val = $this->getSmartEntityProperty($matches[1], $matches[2]);
            $string = str_replace($matches[0], $val, $string);
        }

        return $pyString ? new PyStringNode([$string], $pyString->getLine()) : $string;
    }

    /**
     * Resolve a class name; append App\Entity if it is not present.
     *
     * @param string $class
     *
     * @return string
     */
    private function resolveSmartType($class)
    {
        if ('string' === $class) {
            return 'string';
        }

        if ('UserTransactions' === $class) {
            return "App\\Entity\\Transaction\\$class";
        }

        if (false !== strpos($class, 'App\\Entity')) {
            return $class;
        }

        return "App\\Entity\\$class";
    }

    //
    // Custom ASSERT
    //
    private function assertObjectOfSmartType($object, $type, $message = null)
    {
        $this->assertEquals(
            $this->resolveSmartType($type),
            \is_object($object) ? \get_class($object) : \gettype($object),
            $message ?? 'Entity is not of expected type'
        );
    }

    /**
     * @param null $entityType
     * @param $uniqueIdentifier
     * @param bool $shouldExist
     */
    private function assertSmartEntityInMap($entityType = null, $uniqueIdentifier, $shouldExist = true)
    {
        $exists = isset(self::$map[$uniqueIdentifier]);

        if ($shouldExist) {
            $this->assertTrue($exists, "Entity $entityType with identifier $uniqueIdentifier does not exist");
            if ($entityType) {
                $this->assertObjectOfSmartType(self::$map[$uniqueIdentifier], $entityType);
            }
        } else {// should not exist
            $this->assertFalse($exists, "Entity $entityType with identifier $uniqueIdentifier already exists");
        }
    }
}

<?php

use App\Entity\AdminUser;
use Behat\Behat\Context\Context;
use Behat\Behat\Context\SnippetAcceptingContext;
use Behat\Behat\Hook\Scope\BeforeScenarioScope;
use Behat\MinkExtension\Context\RawMinkContext;
use Behatch\Context\RestContext;
use Behatch\HttpCall\Request;
use Doctrine\ORM\Tools\SchemaTool;
use Lexik\Bundle\JWTAuthenticationBundle\Services\JWTManager;
use Symfony\Component\HttpKernel\KernelInterface;

class AuthContext extends RawMinkContext implements Context, SnippetAcceptingContext
{
    private $jwtManager;

    private $manager;
    private $schemaTool;

    private $classes;
    /**
     * @var Request
     */
    private $request;
    private $doctrine;

    /**
     * @var KernelInterface
     */
    private $kernel;

    /** @var RestContext */
    private $restContext;

    public  $adminUser;

    private static $users = [];

    public $dealerUser;

    /** @var AdminUser|null */
    private $loggedInUser;

    /** @var string|null */
    private $token;

    /** @var \App\Repository\AdminUserRepository */
    private $userRepository;

    private $lastToken;

    protected $userPasswordEncoder;
    protected $container;

    /**
     * Initializes context.
     *
     * Every scenario gets its own context instance.
     * You can also pass arbitrary arguments to the
     * context constructor through behat.yml.
     */
    public function __construct(KernelInterface $kernel, $doctrine, JWTManager $jwtManager, Request $request, \Symfony\Component\Security\Core\Encoder\UserPasswordEncoderInterface $userPasswordEncoder)
    {
        $this->kernel = $kernel;
        $this->manager = $doctrine->getManager();
        $this->jwtManager = $jwtManager;
        $this->schemaTool = new SchemaTool($this->manager);
        $this->classes = $this->manager->getMetadataFactory()->getAllMetadata();
        $this->request = $request;
        $this->doctrine = $doctrine;
        $this->container = $kernel->getContainer();
        $this->userPasswordEncoder = $userPasswordEncoder;
    }

    /**
     * @BeforeScenario
     */
    public function loadDataFixtures(BeforeScenarioScope $scope)
    {
        $this->userRepository = $this->manager->getRepository(AdminUser::class);
        $this->restContext = $scope->getEnvironment()->getContext(RestContext::class);

        $this->adminUser = $this->userRepository->loadUserByUsername('adminuser@gmail.com');
        $this->dealerUser = $this->userRepository->loadUserByUsername('dealer');

        $this->restContext = $scope->getEnvironment()->getContext(RestContext::class);

        if ($this->adminUser) {
            return;
        }

        $userData = new \App\DataFixtures\AppFixtures($this->container,$this->userPasswordEncoder);

        $loader = new \Doctrine\Common\DataFixtures\Loader();
        $loader->addFixture($userData);

        $purger = new \Doctrine\Common\DataFixtures\Purger\ORMPurger();
        $purger->setPurgeMode(\Doctrine\Common\DataFixtures\Purger\ORMPurger::PURGE_MODE_DELETE);

        $executor = new \Doctrine\Common\DataFixtures\Executor\ORMExecutor($this->manager, $purger);
        $executor->execute($loader->getFixtures());

        $this->adminUser = $this->userRepository->loadUserByUsername('adminuser@gmail.com');
        $this->dealerUser = $this->userRepository->loadUserByUsername('dealer');
    }

    /**
     * @AfterScenario
     * @logout
     */
    public function logout()
    {
        $this->restContext->iAddHeaderEqualTo('Authorization', '');
        $this->loggedInUser = null;
        $this->token = null;
    }

    /**
     * @Given /^I am logged in as SuperAdmin/
     */
    public function iAmLoggedInAsAdmin()
    {
        $this->loginUser($this->adminUser);
    }

    /**
     * @Given I am logged in as named :username
     */
    public function iAmLoggedinAsNamed($username)
    {
        $user = $this->userRepository->loadUserByUsername($username);
        $this->loginUser($user);
    }

    /**
     * @Given /^I am logged in as Dealer/
     */
    public function iAmLoggedInAsDealer()
    {
        $this->loginUser($this->dealerUser);
    }

    /**
     * @Then save the User :email named :uniqueIdentifier
     */
    public function saveTheUserNamed($email, $uniqueIdentifier)
    {
        $this->userRepository = $this->manager->getRepository(AdminUser::class);

        switch ($uniqueIdentifier) {
            case 'SuperAdmin':
                $user = $this->userRepository->loadUserByemail($email);
                return $this->users["{$user->username}"] = $user;
            case 'dealer_user':
                return $this->dealerUser = $this->userRepository->loadUserByemail($email);
            default:
                $user = $this->userRepository->loadUserByemail($email);
                return self::$users["{$user->username}"] = $user;
        }
    }

    //
    // UTILS
    //

    public function loginUser($user)
    {
        $this->token = $this->jwtManager->create($user);
        $this->loginWithAccessToken($this->token, $user);
    }

    public function keepLoggedInForNextRequest()
    {
        if ($this->lastToken) {
            $this->restContext->iAddHeaderEqualTo('Authorization', 'Bearer '.$this->lastToken);
        }
    }

    public function loginWithAccessToken($accessToken, $user)
    {
        $this->lastToken = $accessToken;
        $this->restContext->iAddHeaderEqualTo('Authorization', $accessToken ? 'Bearer '.$accessToken : '');
        $this->loggedInUser = $user;
    }

    public function getLastAuthBearer()
    {
        return $this->lastToken;
    }

    public function getLoggedInUser()
    {
        if (null !== $this->loggedInUser && !\is_object($this->loggedInUser)) {
            throw new \LogicException('LoggedIn user should be null or object');
        }

        return $this->loggedInUser;
    }

    public function getToken()
    {
        return $this->token;
    }
}

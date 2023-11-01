<?php

declare(strict_types=1);

use Behat\Gherkin\Node\PyStringNode;
use Behatch\Context\JsonContext as BaseJsonContext;
use Behatch\HttpCall\HttpCallResultPool;
use Behatch\Json\Json;
use PHPUnit\Framework\Assert;
use PHPUnit\Framework\Constraint\Constraint;
use PHPUnit\Framework\ExpectationFailedException;
use SebastianBergmann\Comparator\ComparisonFailure;

final class JsonContext extends BaseJsonContext
{
    public function __construct(HttpCallResultPool $httpCallResultPool)
    {
        parent::__construct($httpCallResultPool);
    }

    private $entityHelperContext;

    /**
     * @BeforeScenario
     */
    public function gatherContexts(\Behat\Behat\Hook\Scope\BeforeScenarioScope $scope)
    {
        $environment = $scope->getEnvironment();
        $this->entityHelperContext = $environment->getContext(EntityHelperContext::class);
    }

    /**
     * @Then /^the JSON should be a superset of:$/
     */
    public function theJsonIsASupersetOf(PyStringNode $content)
    {
        $actual = json_decode($this->httpCallResultPool->getResult()->getValue(), true);

        if(!is_array($actual)){
            var_dump($this->httpCallResultPool->getResult());
            exit;
        }
        Assert::assertArraySubset(json_decode($content->getRaw(), true), $actual);
    }

    /**
     * @Then /^the JSON should not be a superset of:$/
     */
    public function theJsonIsNotASupersetOf(PyStringNode $content)
    {
        $actual = json_decode($this->httpCallResultPool->getResult()->getValue(), true);
        $subset = json_decode($content->getRaw(), true);

        $patched = \array_replace_recursive($actual, $subset);

        $result = $actual != $patched;

        if (!$result) {
            throw new ExpectationFailedException(
                'The JSON should not be a superset',
                new ComparisonFailure($patched, $actual, \var_export($patched, true), \var_export($actual, true))
            );
        }
    }

    /**
     * @Then the JSON node :date should be a valid date
     */
    public function theJsonNodeShouldBeAValidDate($date)
    {
        $actual = json_decode($this->httpCallResultPool->getResult()->getValue(), true);
        $match = preg_match('/\d{4}-\d{2}-\d{2}T\d{2}:\d{2}:\d{2}\+\d{2}:\d{2}/', $actual[$date]);
        Assert::assertTrue((!empty($match)), "$date is not a valid date");
    }

    /**
     * @Then the JSON node :date should be a valid with live Id :liveId
     */
    public function theJsonNodeShouldBeAValidMediaObjectIdWithLiveId($image, $liveId)
    {
        if (!(int) $liveId) {
            $liveId = $this->entityHelperContext->replaceSmartParameters($liveId);
        }

        $actual = json_decode($this->httpCallResultPool->getResult()->getValue(), true);
        $image = $actual[$image];
        Assert::assertTrue((\array_key_exists('contentUrl', $image)), 'Image Array has not a valid contentUrl');

        $contentUrl = $image['contentUrl'];
        $pathInfo = pathinfo($contentUrl);
        $fileNameExplode = explode('_', $pathInfo['filename']);
        Assert::assertEquals($fileNameExplode[0], $liveId, "Image Array has not a valid contentUrl $contentUrl File Prefix Expected $liveId");
    }

    /**
     * @Then the JSON should have :count element(s)
     */
    public function theJsoShouldHaveElements($count)
    {
        $actual = json_decode($this->httpCallResultPool->getResult()->getValue(), true);
        Assert::assertSame($count, \count((array) $actual));
    }

    /**
     * @Then the JSON node :node should be an URL on :protocol
     */
    public function theJsonShouldBeAnUrlOn($node, $protocol)
    {
        $json = $this->getJson();
        $url = $this->inspector->evaluate($json, $node);
        $params = parse_url($url);
        Assert::assertArrayHasKey('scheme', $params);
        $p = $params['scheme'];
        Assert::assertEquals($p, $protocol);
    }

    /**
     * @Then the JSON node :node should be an Object with id :id
     */
    public function theJsonShouldBeAnObjectWithId($node, $id)
    {
        $json = $this->getJson();
        $object = $this->inspector->evaluate($json, $node);
        Assert::assertObjectHasAttribute('id', $object);
        $p = $object->id;
        Assert::assertEquals($p, $id);
    }

    /**
     * Evaluates a PHPUnit\Framework\Constraint matcher object.
     *
     * @throws ExpectationFailedException
     * @throws \SebastianBergmann\RecursionContext\InvalidArgumentException
     */
    public static function assertThatNot($value, Constraint $constraint, string $message = ''): void
    {
        $constraint->evaluate($value, $message);
    }

    /**
     * @Then /^the JSON should be deep equal to:$/
     */
    public function theJsonShouldBeDeepEqualTo(PyStringNode $content)
    {
        $actual = $this->getJson();
        try {
            $expected = new Json($content);
        } catch (\Exception $e) {
            throw new \Exception('The expected JSON is not a valid');
        }

        $actual = new Json(json_encode($actual->getContent()));
        $expected = new Json(json_encode($expected->getContent()));

        $this->assertSame(
            (string) $expected,
            (string) $actual,
            "The json is equal to:\n".$actual->encode()
        );
    }
}

<?php

use Behat\Gherkin\Node\PyStringNode;
use Behat\MinkExtension\Context\RawMinkContext;
use Behat\Symfony2Extension\Context\KernelAwareContext;
use Symfony\Bundle\FrameworkBundle\Client;
use Symfony\Component\HttpFoundation\File\UploadedFile;
use Symfony\Component\HttpKernel\KernelInterface;

final class FileUploadContext extends RawMinkContext implements KernelAwareContext
{
    const DUMMY_FILE_PNG = 'dummy.png';

    public $attachments;

    /** @var KernelInterface */
    private $kernel;

    public function setKernel(KernelInterface $kernelInterface)
    {
        $this->kernel = $kernelInterface;
    }

    /**
     * @Given /^there are dummy files to test$/
     */
    public function thereAreDummyFilesToTest()
    {
        file_put_contents($this->getCacheTestDir().self::DUMMY_FILE_PNG, 'PNG');
    }

    /**
     * @Then /^I attach file to "([^"]*)" field of my request$/
     */
    public function iAttachFilesToMyRequest($fileField)
    {

        $this->attachments[$fileField] = new UploadedFile(
            $this->getMinkParameter("files_path")."/".self::DUMMY_FILE_PNG,
            self::DUMMY_FILE_PNG,
            'image/png',
            3
        );
    }

    /**
     * @param string $method
     * @param string $url
     *
     * @Then /^I send "([^"]*)" request to "([^"]*)" with body:$/
     */
    public function iSendRequestWithBody($method, $url, PyStringNode $data)
    {
        /** @var Client $client */
        $client = $this->getSession()->getDriver()->getClient();
        $client->request($method, $url, [], $this->attachments, [], $data);
    }

    /**
     * @return string
     */
    private function getCacheTestDir()
    {
        return $this->kernel->getCacheDir().'/';
    }
}

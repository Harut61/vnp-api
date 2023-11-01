<?php
namespace App\Services\Aws;

use Aws\Sqs\SqsClient;
use Aws\Credentials\Credentials;

class SqsService
{
    /**
     * @var SqsClient 
     */
    private $client;
    
    protected $container;

    public function __construct()
    {
        $this->setClient(getenv("AWS_SQS_ACCESS_KEY"),getenv("AWS_SQS_SECRET_KEY"),getenv("AWS_SQS_REGION"));
    }

    /**
     * @param $sqs_access_key
     * @param $sqs_secret_key
     * @param string $region
     * @param string $version
     * @return SqsClient
     */
    public function setClient($sqs_access_key, $sqs_secret_key, $region = 'us-east-1', $version = 'latest')
    {
        $credentials = new Credentials($sqs_access_key, $sqs_secret_key);

        $this->client = new SqsClient(array(
            'credentials' => $credentials,
            'region' => $region,
            "version" => $version
        ));

        return $this->client;
    }
    
    public function getClient()
    {
        if (!$this->client instanceof SqsClient) {
            throw new \Exception("SqsClient is not set");
        }
        
        return $this->client;
    }
    
    public function receiveMessage($queueUrl, $waitTimeSeconds = 20, $maxNumberOfMessages = 1)
    {
        $results = $this->getClient()->receiveMessage([
            'QueueUrl'            => $queueUrl,
            'WaitTimeSeconds'     => $waitTimeSeconds,
            'MaxNumberOfMessages' => $maxNumberOfMessages
        ]);
        
        return $results->get('Messages');
    }
    
    public function receiveOneMessage($queueUrl)
    {
        $messages = $this->receiveMessage($queueUrl);
        return $messages[0];
    }
    
    public function sendMessage($queueUrl, $message)
    {
        $this->getClient()->sendMessage([
            'QueueUrl'    => $queueUrl,
            'MessageBody' => $message,
        ]);
    }
    
    public function deleteMessage($queueUrl, $handle)
    {
        $this->getClient()->deleteMessage([
            'QueueUrl'      => $queueUrl,
            'ReceiptHandle' => $handle
        ]);
    }
    
}
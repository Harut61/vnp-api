<?php
namespace App\Util;

use App\Model\Message;
use Aws\Credentials\Credentials;
use Aws\Result;
use Aws\Sdk;
use Aws\Sqs\SqsClient;
use Symfony\Component\HttpKernel\EventListener\ValidateRequestListener;

class AwsSqsUtil implements AwsSqsUtilInterface
{
    /** @var SqsClient */
    private $client;
    private $applicationName;
    private $env;

    public function __construct(Sdk $sdk, string $applicationName, string $env)
    {
        $this->applicationName = $applicationName;
        $this->env = $env;
    }

    public function createClient(string $sqs_access_key, string $sqs_secret_key, string $region, string $version): void
    {
        $credentials = new Credentials($sqs_access_key, $sqs_secret_key);

        $this->client = new SqsClient(array(
            'credentials' => $credentials,
            'region' => $region,
            "version" => $version
        ));
    }

    /**
     * @link https://docs.aws.amazon.com/aws-sdk-php/v3/api/api-sqs-2012-11-05.html#getqueueurl
     */
    public function getQueueUrl(string $name): ?string
    {

        /** @var Result $result */
        $result = $this->client->getQueueUrl([
            'QueueName' => $this->createQueueName($name),
        ]);

        return $result->get('QueueUrl');
    }

    /**
     * @link https://docs.aws.amazon.com/aws-sdk-php/v3/api/api-sqs-2012-11-05.html#getqueueurl
     */
    public function getQueueUrlFromFullName(string $name): ?string
    {

        /** @var Result $result */
        $result = $this->client->getQueueUrl([
            'QueueName' => $name,
        ]);

        return $result->get('QueueUrl');
    }

    /**
     * @link https://docs.aws.amazon.com/aws-sdk-php/v3/api/api-sqs-2012-11-05.html#receivemessage
     */
    public function receiveMessage(string $url): ?Message
    {
        /** @var Result $result */
        $result = $this->client->receiveMessage([
            'QueueUrl' => $url,
            'MaxNumberOfMessages' => 1,
        ]);

        $message = null;
        if (null !== $result->get('Messages')) {
            $message = new Message();
            $message->url = $url;
            $message->id = $result->get('Messages')[0]['MessageId'];
            $message->body = $result->get('Messages')[0]['Body'];
            $message->receiptHandle = $result->get('Messages')[0]['ReceiptHandle'];
        }

        return $message;
    }

    /**
     * @link https://docs.aws.amazon.com/aws-sdk-php/v3/api/api-sqs-2012-11-05.html#deletemessage
     */
    public function deleteMessage(Message $message): void
    {
        $this->client->deleteMessage([
            'QueueUrl' => $message->url,
            'ReceiptHandle' => $message->receiptHandle,
        ]);
    }


    /**
     * @param $url
     * @param $message
     * https://docs.aws.amazon.com/aws-sdk-php/v3/api/api-sqs-2012-11-05.html#sendmessage
     */
    public function sendMessage($url, $message): void
    {
        $this->client->sendMessage([
            "QueueUrl" => $url,
            "MessageBody" => $message
        ]);
    }


    /**
     * @param $url
     * @param $message
     * @param $messageGroupId
     *  https://docs.aws.amazon.com/aws-sdk-php/v3/api/api-sqs-2012-11-05.html#sendmessage
     */
    public function sendMessageFifo($url, $message, $messageGroupId): void
    {
        $this->client->sendMessage([
            "QueueUrl" => $url,
            "MessageBody" => $message,
            "MessageGroupId" => $messageGroupId,
            "MessageGroupId" => $messageGroupId
        ]);
    }

    /**
     * @link https://docs.aws.amazon.com/aws-sdk-php/v3/api/api-sqs-2012-11-05.html#changemessagevisibility
     */
    public function requeueMessage(Message $message): void
    {
        $this->client->changeMessageVisibility([
            'QueueUrl' => $message->url,
            'ReceiptHandle' => $message->receiptHandle,
            'VisibilityTimeout' => 30,
        ]);
    }

    public function createQueueName(string $name, bool $isDeadLetter = null): string
    {
        return strtolower(sprintf(
            '%s-%s-%s%s',
            strtoupper($this->applicationName),
            strtoupper($this->env),
            $name,
            $isDeadLetter ? '-dead-letter' : null
        ));
    }
}

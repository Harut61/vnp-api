<?php
namespace App\Util;

use App\Model\Message;

/**
 * Interface AwsSqsUtilInterface
 * @package App\Util
 */
interface AwsSqsUtilInterface
{
    public function createClient(string $sqs_access_key, string $sqs_secret_key, string $region, string $version): void;

    public function getQueueUrl(string $name): ?string;

    public function receiveMessage(string $url): ?Message;

    public function deleteMessage(Message $message): void;

    public function requeueMessage(Message $message): void;
}
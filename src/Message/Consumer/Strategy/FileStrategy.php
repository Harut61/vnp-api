<?php

namespace App\Message\Consumer\Strategy;

use App\Model\Message;
use App\Util\AwsSqsUtilInterface;
use Psr\Log\LoggerInterface;

class FileStrategy implements StrategyInterface
{
    public const QUEUE_NAME = 'file';

    private $awsSqsUtil;
    private $logger;

    public function __construct(
        AwsSqsUtilInterface $awsSqsUtil,
        LoggerInterface $logger
    ) {
        $this->awsSqsUtil = $awsSqsUtil;
        $this->logger = $logger;
    }

    public function canProcess(string $queue): bool
    {
        return self::QUEUE_NAME === $queue;
    }

    public function process(Message $message): void
    {
        $body = json_decode($message->body, true);

        if ($body['is_good_message']) {
            $this->awsSqsUtil->deleteMessage($message);

            $this->logger->info(sprintf('The message "%s" has been consumed.', $message->id));
        } else {
            $this->awsSqsUtil->requeueMessage($message);

            $this->logger->alert(sprintf('The message "%s" has been put in the "flight" mode.', $message->id));
        }
    }
}

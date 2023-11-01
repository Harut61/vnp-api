<?php

namespace App\Message\Consumer\Strategy;

use App\Model\Message;
use App\Util\AwsSqsUtilInterface;
use Psr\Log\LoggerInterface;

class CommentStrategy implements StrategyInterface
{
    public const QUEUE_NAME = 'comment';

    private $awsSqsUtil;
    private $logger;

    /**
     * CommentStrategy constructor.
     * @param AwsSqsUtilInterface $awsSqsUtil
     * @param LoggerInterface $logger
     */
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
            $this->logger->info(sprintf('The message "%s" has been consumed.', $message->id));
        } else {
            $this->logger->alert(sprintf('The message "%s" has been deleted.', $message->id));
        }

        $this->awsSqsUtil->deleteMessage($message);
    }
}
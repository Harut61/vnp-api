<?php
namespace App\Message\Consumer;

use App\Message\Consumer\Strategy\StrategyInterface;
use App\Model\Message;

class Consumer implements ConsumerInterface
{
    private $strategies;

    public function __construct(\Traversable $strategies)
    {
        $this->strategies = $strategies;
    }

    public function consume(Message $message, string $queue): void
    {
        /** @var StrategyInterface $strategy */
        foreach ($this->strategies as $strategy) {
            if ($strategy->canProcess($queue)) {
                $strategy->process($message);

                break;
            }
        }
    }
}
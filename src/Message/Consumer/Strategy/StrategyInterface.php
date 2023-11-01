<?php
namespace App\Message\Consumer\Strategy;

use App\Model\Message;

interface StrategyInterface
{
    public function canProcess(string $queue): bool;

    public function process(Message $message): void;
}
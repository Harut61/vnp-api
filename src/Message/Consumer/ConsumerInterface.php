<?php
namespace App\Message\Consumer;

use App\Model\Message;

interface ConsumerInterface
{
    public function consume(Message $message, string $queue): void;
}
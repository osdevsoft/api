<?php

namespace Osds\Api\Application\Replicate;

class LogConsumer
{

    public function __construct()
    {

    }

    public function execute($message)
    {
        echo PHP_EOL . "logging " . $message->getBody();
    }
}
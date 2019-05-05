<?php

namespace Osds\Api\Application\Replicate;

class ReplicateForQueryConsumer
{

//    private $command;

    public function __construct(
//        ReplicateEntityForQueryCommandHandler $commandHandler
    )
    {
//        $this->command = $commandHandler;
    }

    public function execute($message)
    {
//        $command = unserialize($message->getBody());
//        $uuid = $this->command->handle($command, true);
        file_put_contents('/tmp/Replicate', $message->getBody());
    }

}
<?php

namespace Osds\Api\Application\Replicate;

class ReplicateForQueryConsumer
{

//    private $command;

    public function __construct(
        ReplicateForQueryCommandHandler $commandHandler
    )
    {
        $this->command = $commandHandler;
    }

    public function execute($message)
    {
        $originCommand = unserialize($message->getBody());
        $command = new ReplicateForQueryCommand(
            $originCommand->entity(),
            $originCommand->uuid(),
            $originCommand->data()
        );

        $uuid = $this->command->handle($command, true);
//        file_put_contents('/tmp/Replicate', $message->getBody());
    }

}
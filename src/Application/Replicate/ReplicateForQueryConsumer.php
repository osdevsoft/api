<?php

namespace Osds\Api\Application\Replicate;

class ReplicateForQueryConsumer
{

    private $command;

    public function __construct(
        ReplicateForQueryCommandHandler $commandHandler
    )
    {
        $this->command = $commandHandler;
        echo "create";
    }

    public function execute($message)
    {
        $originCommand = unserialize($message->getBody());
        $command = new ReplicateForQueryCommand(
            $originCommand->entity(),
            $originCommand->uuid(),
            $originCommand->data()
        );
        echo "cosume";
        $uuid = $this->command->handle($command, true);
    }

}
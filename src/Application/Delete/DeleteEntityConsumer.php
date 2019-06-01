<?php

namespace Osds\Api\Application\Delete;

class DeleteEntityConsumer
{

    private $command;

    public function __construct(
        DeleteEntityCommandHandler $commandHandler
    ) {
        $this->command = $commandHandler;
    }

    public function execute($message)
    {
        $command = unserialize($message->getBody());
        $uuid = $this->command->handle($command, true);
    }
}

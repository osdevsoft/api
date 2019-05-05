<?php

namespace Osds\Api\Application\Update;

class UpdateEntityConsumer
{

    private $command;

    public function __construct(
        UpdateEntityCommandHandler $commandHandler
    )
    {
        $this->command = $commandHandler;
    }

    public function execute($message)
    {
        $command = unserialize($message->getBody());
        $uuid = $this->command->handle($command, true);

    }

}
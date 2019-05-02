<?php

namespace Osds\Api\Application\Insert;

class InsertEntityConsumer
{

    private $command;

    public function __construct(
        InsertEntityCommandHandler $commandHandler
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
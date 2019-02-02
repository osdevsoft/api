<?php

namespace Osds\Api\Application\Delete;

use Osds\Api\Domain\Bus\Command\Command;
use Osds\Api\Domain\Bus\Command\CommandBus;

class DeleteEntityCommandBus implements CommandBus
{

    private $command_handler;

    public function __construct(DeleteEntityCommandHandler $command_handler)
    {
        $this->command_handler = $command_handler;
    }

    public function dispatch(Command $command)
    {
        return $this->command_handler->handle($command);
    }

}
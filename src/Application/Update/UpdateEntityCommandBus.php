<?php

namespace Osds\Api\Application\Update;

use Osds\Api\Domain\Bus\Command\Command;
use Osds\Api\Domain\Bus\Command\CommandBus;

class UpdateEntityCommandBus implements CommandBus
{

    private $command_handler;

    public function __construct(UpdateEntityCommandHandler $command_handler)
    {
        $this->command_handler = $command_handler;
    }

    public function dispatch(Command $command)
    {
        return $this->command_handler->handle($command);
    }

}
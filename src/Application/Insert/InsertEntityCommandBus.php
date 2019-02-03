<?php

namespace Osds\Api\Application\Insert;

use Osds\Api\Domain\Bus\Command\Command;
use Osds\Api\Domain\Bus\Command\CommandBus;

class InsertEntityCommandBus implements CommandBus
{

    private $command_handler;

    public function __construct(InsertEntityCommandHandler $command_handler)
    {
        $this->command_handler = $command_handler;
    }

    public function dispatch(Command $command)
    {
        return $this->command_handler->handle($command);
    }

}
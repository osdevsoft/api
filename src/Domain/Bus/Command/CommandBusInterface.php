<?php

namespace Osds\Api\Domain\Bus\Command;

interface CommandBusInterface
{
    public function dispatch(Command $command, $forceExecution);
}

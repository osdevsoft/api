<?php

namespace Osds\Api\Domain\Bus\Command;

interface CommandBus
{
    public function dispatch(Command $command): void;
}

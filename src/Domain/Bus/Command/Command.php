<?php

declare(strict_types = 1);

namespace Osds\Api\Domain\Bus\Command;

interface Command
{

    public function getPayload();

    public function setQueue($queue);

    public function getQueue();

}
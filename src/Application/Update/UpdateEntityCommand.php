<?php

namespace Osds\Api\Application\Update;

use Osds\Api\Domain\Bus\Command\Command;

final class UpdateEntityCommand implements Command
{

    private $entity;

    private $uuid;

    private $data;

    private $queue = null;

    public function __construct(
        string $entity,
        string $uuid,
        $data
    ) {
        $this->entity = $entity;
        $this->uuid = $uuid;
        $this->data = $data;
    }

    public function entity(): string
    {
        return $this->entity;
    }

    public function uuid(): string
    {
        return $this->uuid;
    }

    public function data()
    {
        return $this->data;
    }

    public function getPayload(): string
    {
        return serialize($this);
    }

    public function setQueue($queue)
    {
        $this->queue = $queue;
    }

    public function getQueue()
    {
        return $this->queue;
    }


}
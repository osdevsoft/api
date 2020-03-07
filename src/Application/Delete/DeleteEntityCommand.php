<?php

namespace Osds\Api\Application\Delete;


use Osds\Api\Domain\Bus\Command\Command;

final class DeleteEntityCommand implements Command
{

    private $entity;

    private $uuid;

    private $data;

    private $queue = null;

    public function __construct(
        string $entity,
        string $uuid
    ) {
        $this->entity = $entity;
        $this->uuid = $uuid;
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

    public function getQueue():? string
    {
        return $this->queue;
    }
}

<?php

namespace Osds\Api\Application\Update;

use Osds\Api\Domain\Bus\Command\Command;

final class UpdateEntityCommand implements Command
{

    private $entity;

    private $uuid;

    public function __construct(
        string $entity,
        string $uuid,
        array $data
    )
    {
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

    public function data(): array
    {
        return $this->data;
    }

    public function getPayload(): string
    {
        return json_encode(
            [
                'entity' => $this->entity,
                'uuid' => $this->uuid,
                'data' => $this->data
            ]
        );
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
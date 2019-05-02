<?php

namespace Osds\Api\Application\Delete;


use Osds\Api\Domain\Bus\Command\Command;

final class DeleteEntityCommand implements Command
{

    private $entity;

    private $uuid;

    public function __construct(
        string $entity,
        string $uuid
    )
    {
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

    public function getPayload(): string
    {
        return json_encode(
            [
                'entity' => $this->entity,
                'uuid' => $this->uuid
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
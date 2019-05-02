<?php

namespace Osds\Api\Application\Delete;


use Osds\Api\Domain\Bus\Command\Command;

final class DeleteEntityCommand implements Command
{

    private $entity;

    private $id;

    public function __construct(
        string $entity,
        string $id
    )
    {
        $this->entity = $entity;
        $this->id = $id;
    }

    public function entity(): string
    {
        return $this->entity;
    }

    public function id(): string
    {
        return $this->id;
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
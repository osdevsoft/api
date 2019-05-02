<?php

namespace Osds\Api\Application\Update;

use Osds\Api\Domain\Bus\Command\Command;

final class UpdateEntityCommand implements Command
{

    private $entity;

    private $id;

    public function __construct(
        string $entity,
        string $id,
        array $data
    )
    {
        $this->entity = $entity;
        $this->id = $id;
        $this->data = $data;
    }

    public function entity(): string
    {
        return $this->entity;
    }

    public function id(): string
    {
        return $this->id;
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
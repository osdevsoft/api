<?php
namespace Osds\Api\Application\Replicate;

use Osds\Api\Domain\Bus\Command\Command;

final class ReplicateForQueryCommand implements Command
{
    private $entity;

    private $uuid;

    private $data;

    private $originCommand;

    private $queue = null;

    public function __construct(
        string $entity,
        string $uuid,
        $data,
        string $originCommand
    )
    {
        $this->entity = $entity;
        $this->uuid = $uuid;
        $this->data = $data;
        $this->originCommand = $originCommand;
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

    public function originCommand(): string
    {
        return $this->originCommand;
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
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

}
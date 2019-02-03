<?php

namespace Osds\Api\Application\Insert;

use Osds\Api\Domain\Bus\Command\Command;

final class InsertEntityCommand implements Command
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
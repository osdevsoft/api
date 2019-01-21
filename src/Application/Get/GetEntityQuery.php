<?php

namespace Osds\Api\Application\Get;

use Osds\Api\Domain\Bus\Query\Query;

final class GetEntityQuery implements Query
{
    private $id;

    public function __construct(string $id)
    {
        $this->id = $id;
    }

    public function id(): string
    {
        return $this->id;
    }
}
<?php

namespace Osds\Api\Application\Auth;

use Osds\Api\Domain\Bus\Query\Query;

final class ServiceAuthQuery implements Query
{

    private $entity;

    private $username;

    public function __construct(
        string $entity,
        string $username
    ) {
        $this->entity = $entity;
        $this->username = $username;
    }

    public function entity(): string
    {
        return $this->entity;
    }

    public function username(): string
    {
        return $this->username;
    }
}

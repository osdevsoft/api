<?php

namespace Osds\Api\Application\Auth;

use Osds\Api\Domain\Bus\Query\Query;

final class LoginUserQuery implements Query
{

    private $entity;

    private $email;

    public function __construct(
        string $entity,
        string $email
    ) {
        $this->entity = $entity;
        $this->email = $email;
    }

    public function entity(): string
    {
        return $this->entity;
    }

    public function email(): string
    {
        return $this->email;
    }
}

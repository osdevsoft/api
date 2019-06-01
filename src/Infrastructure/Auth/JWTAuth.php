<?php

namespace Osds\Api\Infrastructure\Auth;

class JWTAuth implements AuthInterface
{

    private $manager;

    public function __construct($manager)
    {
        $this->manager = $manager;
    }

    public function manager()
    {
        return $this->manager;
    }
}

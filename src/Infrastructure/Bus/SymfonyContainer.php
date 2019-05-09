<?php

namespace Osds\Api\Infrastructure\Bus;

use Osds\Api\Infrastructure\Bus\ContainerInterface;
use Symfony\Component\DependencyInjection\ContainerInterface as SymfonyContainerInterface;

class SymfonyContainer implements ContainerInterface {

    private $handler;

    public function __construct(
        SymfonyContainerInterface $handler
    )
    {
        $this->handler = $handler;
    }

    public function handler()
    {
        return $this->handler;
    }

}
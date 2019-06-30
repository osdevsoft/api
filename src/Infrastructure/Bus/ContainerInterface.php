<?php

namespace Osds\Api\Infrastructure\Bus;

interface ContainerInterface
{
    public function get($name);
}

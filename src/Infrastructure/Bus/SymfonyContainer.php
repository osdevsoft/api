<?php

namespace Osds\Api\Infrastructure\Bus;

use Osds\Api\Infrastructure\Bus\ContainerInterface;
use Symfony\Component\DependencyInjection\ContainerInterface as SymfonyContainerInterface;

class SymfonyContainer implements SymfonyContainerInterface, ContainerInterface {

    public function set($id, $service) {}

    public function get($id, $invalidBehavior = self::EXCEPTION_ON_INVALID_REFERENCE) {}

    public function has($id) {}

    public function initialized($id) {}

    public function getParameter($name) {}

    public function hasParameter($name) {}

    public function setParameter($name, $value) {}

}
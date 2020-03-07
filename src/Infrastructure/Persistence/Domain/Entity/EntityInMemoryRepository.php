<?php

namespace Osds\Api\Infrastructure\Persistence\Domain\Entity;

use Osds\Api\Domain\Entity\EntityRepositoryInterface;
use Osds\Api\Infrastructure\Persistence\InMemoryRepository;

class EntityInMemory extends InMemoryRepository implements EntityRepositoryInterface
{
}

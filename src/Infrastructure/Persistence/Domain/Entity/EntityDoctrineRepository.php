<?php

namespace Osds\Api\Infrastructure\Persistence\Domain\Entity;

use Osds\Api\Domain\Entity\EntityRepositoryInterface;
use Osds\Api\Infrastructure\Persistence\DoctrineRepository;

class EntityDoctrineRepository extends DoctrineRepository implements EntityRepositoryInterface
{
}

<?php

namespace Osds\Api\Infrastructure\Persistence\Domain\Entity;

use Osds\Api\Domain\Entity\EntityRepositoryInterface;
use Osds\DDDCommon\Infrastructure\Persistence\DoctrineRepository;

class EntityDoctrineRepository extends DoctrineRepository implements EntityRepositoryInterface
{
}

<?php

namespace Osds\Api\Infrastructure\Persistence\Domain\Entity;

use Osds\Api\Domain\Entity\EntityRepositoryInterface;
use Osds\Api\Infrastructure\Persistence\ElasticCacheRepository;

class EntityElasticCacheRepository extends ElasticCacheRepository implements EntityRepositoryInterface
{
}

<?php

namespace Osds\Api\Infrastructure\Persistence\Domain\Entity;

use Osds\Api\Domain\Entity\EntityRepositoryInterface;
use Osds\DDDCommon\Infrastructure\Persistence\CsvRepository;

class EntityCsvRepository extends CsvRepository implements EntityRepositoryInterface
{
}

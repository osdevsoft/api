<?php

namespace Osds\Api\Application\Find;

use Osds\Api\Domain\Bus\Query\Query;

final class FindEntityQuery implements Query
{

    private $entity;

    private $searchFields;

    private $queryFilters;

    private $additionalRequests;

    public function __construct(
        string $entity,
        Array $searchFields = []
    ) {
        $this->entity = $entity;
        $this->searchFields = $searchFields;
    }

    public function entity(): string
    {
        return $this->entity;
    }

    public function searchFields(): array
    {
        return $this->searchFields;
    }

    public function queryFilters(): array
    {
        return $this->queryFilters;
    }

    public function additionalRequests(): array
    {
        return $this->additionalRequests;
    }
}

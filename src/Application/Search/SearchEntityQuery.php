<?php

namespace Osds\Api\Application\Search;

use Osds\Api\Domain\Bus\Query\Query;

final class SearchEntityQuery implements Query
{

    private $entity;

    private $searchFields;

    private $queryFilters;

    private $additionalRequests;

    public function __construct(
        string $entity,
        Array $searchFields = [],
        Array $queryFilters = [],
        Array $additionalRequests = []
    ) {
        $this->entity = $entity;
        $this->searchFields = $searchFields;
        $this->queryFilters = $queryFilters;
        $this->additionalRequests = $additionalRequests;
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

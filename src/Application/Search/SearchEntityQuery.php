<?php

namespace Osds\Api\Application\Search;

use Osds\Api\Domain\Bus\Query\Query;

final class SearchEntityQuery implements Query
{

    private $entity;

    private $search_fields;

    private $query_filters;

    public function __construct(
        string $entity,
        Array $search_fields,
        Array $query_filters
    )
    {
        $this->entity = $entity;
        $this->search_fields = $search_fields;
        $this->query_filters = $query_filters;
    }

    public function entity(): string
    {
        return $this->entity;
    }

    public function searchFields(): array
    {
        return $this->search_fields;
    }

    public function queryFilters(): array
    {
        return $this->query_filters;
    }


}
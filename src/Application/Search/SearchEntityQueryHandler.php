<?php

namespace Osds\Api\Application\Search;

use Osds\Api\Domain\Bus\Query\QueryHandler;

final class SearchEntityQueryHandler implements QueryHandler
{
    private $use_case;

    public function __construct(SearchEntityUseCase $use_case)
    {
        $this->use_case = $use_case;
    }

    public function handle(SearchEntityQuery $query)
    {
        return $this->use_case->execute(
            $query->entity(),
            $query->searchFields(),
            $query->queryFilters()
            );
    }
}

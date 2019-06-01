<?php

namespace Osds\Api\Application\Search;

use Osds\Api\Domain\Bus\Query\QueryHandler;

final class SearchEntityQueryHandler implements QueryHandler
{
    private $useCase;

    public function __construct(SearchEntityUseCase $useCase)
    {
        $this->useCase = $useCase;
    }

    public function handle(SearchEntityQuery $query)
    {
        return $this->useCase->execute(
            $query->entity(),
            $query->searchFields(),
            $query->queryFilters(),
            $query->additionalRequests()
        );
    }
}

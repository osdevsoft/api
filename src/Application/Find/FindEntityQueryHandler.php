<?php

namespace Osds\Api\Application\Find;

use Osds\Api\Domain\Bus\Query\QueryHandler;

final class FindEntityQueryHandler implements QueryHandler
{
    private $useCase;

    public function __construct(
        FindEntityUseCase $useCase
    ) {
        $this->useCase = $useCase;
    }

    public function handle(FindEntityQuery $query)
    {
        return $this->useCase->execute(
            $query->entity(),
            $query->searchFields(),
            $query->queryFilters(),
            $query->additionalRequests()
        );
    }
}

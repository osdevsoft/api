<?php

namespace Osds\Api\Application\Get;

use Osds\Api\Domain\Bus\Query\QueryHandler;

final class GetEntityQueryHandler implements QueryHandler
{
    private $useCase;

    public function __construct(GetEntityUseCase $useCase)
    {
        $this->useCase = $useCase;
    }

    public function handle(GetEntityQuery $query)
    {
        return $this->useCase->execute(
            $query->entity(),
            $query->searchFields(),
            $query->queryFilters()
            );
    }
}

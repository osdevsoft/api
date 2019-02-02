<?php

namespace Osds\Api\Application\Get;

use Osds\Api\Domain\Bus\Query\QueryHandler;

final class GetEntityQueryHandler implements QueryHandler
{
    private $use_case;

    public function __construct(GetEntityUseCase $use_case)
    {
        $this->use_case = $use_case;
    }

    public function handle(GetEntityQuery $query)
    {
        return $this->use_case->execute(
            $query->entity(),
            $query->searchFields(),
            $query->queryFilters()
            );
    }
}

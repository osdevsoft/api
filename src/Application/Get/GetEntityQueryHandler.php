<?php

namespace Osds\Api\Application\Get;

use Osds\Api\Domain\Bus\Query\QueryHandler;
use function Lambdish\Phunctional\apply;
use function Lambdish\Phunctional\pipe;

final class GetEntityQueryHandler implements QueryHandler
{
    private $getter;

    public function __construct(GetEntityUseCase $finder)
    {
        $this->getter = pipe($finder, new EntityResponseConverter());
    }

    public function __invoke(GetEntityQuery $query): EntityResponse
    {
        $id = new EntityId($query->id());

        return apply($this->getter, [$id]);
    }
}

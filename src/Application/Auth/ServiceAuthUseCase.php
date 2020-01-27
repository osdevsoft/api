<?php

namespace Osds\Api\Application\Auth;

use Osds\Api\Application\Find\FindEntityQuery;
use Osds\Api\Domain\Bus\Query\QueryBus;

final class ServiceAuthUseCase
{
    private $queryBus;

    public function __construct(QueryBus $queryBus)
    {
        $this->queryBus = $queryBus;
    }

    public function execute($entity, $username)
    {
        $messageObject = new FindEntityQuery(
            $entity,
            ['username' => $username]
        );

        return $this->queryBus->ask($messageObject);
    }
}

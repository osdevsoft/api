<?php

namespace Osds\Api\Application\Auth;

use Osds\Api\Application\Search\SearchEntityQuery;
use Osds\Api\Domain\Bus\Query\QueryBus;

final class LoginUserUseCase
{
    private $queryBus;

    public function __construct(QueryBus $queryBus)
    {
        $this->queryBus = $queryBus;
    }

    public function execute($entity, $email)
    {
        $messageObject = new SearchEntityQuery(
            $entity,
            ['email' => $email]
        );

        return $this->queryBus->ask($messageObject);
    }
}

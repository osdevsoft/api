<?php

namespace Osds\Api\Application\Auth;

use Osds\Api\Domain\Bus\Query\QueryHandler;

final class ServiceAuthQueryHandler implements QueryHandler
{
    private $useCase;

    public function __construct(ServiceAuthUseCase $useCase)
    {
        $this->useCase = $useCase;
    }

    public function handle(ServiceAuthQuery $query)
    {
        return $this->useCase->execute(
            $query->entity(),
            $query->email()
        );
    }
}

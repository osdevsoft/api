<?php

namespace Osds\Api\Application\Auth;

use Osds\Api\Domain\Bus\Query\QueryHandler;

final class LoginUserQueryHandler implements QueryHandler
{
    private $useCase;

    public function __construct(LoginUserUseCase $useCase)
    {
        $this->useCase = $useCase;
    }

    public function handle(LoginUserQuery $query)
    {
        return $this->useCase->execute(
            $query->entity(),
            $query->email()
        );
    }
}

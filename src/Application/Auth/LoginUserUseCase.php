<?php

namespace Osds\Api\Application\Auth;

use Osds\Api\Application\Search\SearchEntityUseCase;

final class LoginUserUseCase
{
    private $searchEntityUseCase;

    public function __construct(SearchEntityUseCase $searchEntityUseCase)
    {
        $this->searchEntityUseCase = $searchEntityUseCase;
    }

    public function execute($entity, $email)
    {
        $searchFields = [
            'email' => $email
        ];
        #retrieve the data from the database, using the specified repository
        $result_data = $this->searchEntityUseCase->execute(
            $entity,
            $searchFields
        );

        return $result_data;

    }
}

<?php

namespace Osds\Api\Application\Get;

use Osds\Api\Domain\Bus\Query\Query;
use Osds\Api\Domain\Bus\Query\QueryBus;

class GetEntityQueryBus implements QueryBus
{

    public function ask(Query $query)
    {
        $repository = new GetEntityRepository();
        $use_case = new GetEntityUseCase($repository);

        #$model_object_handler = get_class($query) . 'Handler';

        $model_object_handler_instance = new GetEntityQueryHandler($use_case);
        return $model_object_handler_instance->handle($query);


    }

}
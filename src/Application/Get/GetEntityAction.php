<?php

namespace Osds\Api\Application\Get;

use Osds\Api\Application\BaseAction;

class GetEntityAction extends BaseAction
{
    public function __invoke(string $id)
    {
        return $this->ask(new GetEntityQuery($id));
    }
}
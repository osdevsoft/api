<?php

namespace Osds\Api\Domain\Bus\Query;

interface QueryBus
{
    public function ask(Query $query): ?Response;
}

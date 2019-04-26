<?php

namespace Osds\Api\Domain\Bus\Query;

interface QueryBusInterface
{
    public function ask(Query $query);
}

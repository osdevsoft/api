<?php

namespace Osds\Api\Application;

use Osds\Api\Domain\Bus\Command\Command;
use Osds\Api\Domain\Bus\Command\CommandBus;
use Osds\Api\Domain\Bus\Query\Query;
use Osds\Api\Domain\Bus\Query\QueryBus;

class BaseAction
{
    protected $repository;

    public $entity;

    public $request;
    
    public $services;

    public $entity_id = null;

    private $queryBus;

    private $commandBus;

    public function __construct(
        QueryBus $queryBus,
        CommandBus $commandBus = null
    ) {
        $this->queryBus         = $queryBus;
        $this->commandBus       = $commandBus;
    }


    /**
     * BaseCommand constructor.
     * @param null $request : get / post arguments. If uri has an id, it's assigned to $this->entity_id
     */
/*    public function getMessageObject($entity, $request = null) {

        $this->entity = $entity;
        #request (get / post / custom (set by the app) parameters
        $this->request = $request;

    }*/

    protected function ask(Query $query)
    {
        return $this->queryBus->ask($query);
    }

    protected function dispatch(Command $command)
    {
        $this->commandBus->dispatch($command);
    }
}
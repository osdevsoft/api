<?php

/**
 * Base class for Commands that does basic stuff (normalize request params and set a repository)
 */
namespace Osds\Api\Application;

use App\Application\Helpers;
use Osds\Api\Infrastructure\Repositories\ApiRepository;
use Osds\Api\Infrastructure\Repositories\DoctrineModelRepository;
use Osds\Api\Infrastructure\Repositories\EloquentModelRepository;

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
        CommandBus $commandBus
    ) {
        $this->queryBus         = $queryBus;
        $this->commandBus       = $commandBus;
    }


    /**
     * BaseCommand constructor.
     * @param null $repository_model : entity that is going to be treated
     * @param null $parameters : get / post arguments. If uri has an id, it's assigned to $this->entity_id
     */
    public function setBaseSettings($entity = null, $request = null) {

//        $this->repository = $this->getRepository();

//        $this->repository->setEntity($entity);

        #request (get / post / custom (set by the app) parameters
        $this->request = $request;

        #services we may need
//        $this->services = Helpers::getRequiredServices();

        // id comes from Internal API Requests, get or post (not by query string)
        if (
            (isset($this->request->custom_parameters) && $this->request->custom_parameters->entity_id == null)
            && isset($this->request->parameters['id'])
        ) {
            $this->request->custom_parameters->entity_id = $this->request->parameters['id'];
        }

    }

//    /**
//     * Gets the repository to use
//     *
//     * @return DoctrineModelRepository|EloquentModelRepository
//     */
//    private function getRepository()
//    {
//        return new DoctrineModelRepository();
//    }

    protected function ask(Query $query)
    {
        return $this->queryBus->ask($query);
    }

    protected function dispatch(Command $command)
    {
        $this->commandBus->dispatch($command);
    }
}
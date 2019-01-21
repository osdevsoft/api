<?php

/**
 * Base class for Commands that does basic stuff (normalize request params and set a repository)
 */
namespace Osds\Api\Application\Commands;

use App\Application\Helpers;
use Osds\Api\Infrastructure\Repositories\ApiRepository;
use Osds\Api\Infrastructure\Repositories\DoctrineModelRepository;
use Osds\Api\Infrastructure\Repositories\EloquentModelRepository;

class BaseCommand
{
    protected $repository;

    public $entity;

    public $request;
    
    public $services;

    public $entity_id = null;

    /**
     * BaseCommand constructor.
     * @param null $repository_model : entity that is going to be treated
     * @param null $parameters : get / post arguments. If uri has an id, it's assigned to $this->entity_id
     */
    public function __construct($entity = null, $request = null) {

        $this->repository = $this->getRepository();

        $this->repository->setEntity($entity);

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

    /**
     * Gets the repository to use
     *
     * @param $repository
     * @return DoctrineModelRepository|EloquentModelRepository
     */
    private function getRepository()
    {

        if(class_exists('Illuminate\Database\Eloquent\Model'))
        {
            $repository = new EloquentModelRepository();
        } else {
            $repository = new DoctrineModelRepository();
        }

        return $repository;
    }

    /**
     * generates a ucfirst-format name for the model requested (not in use currently)
     *
     * @param $model
     * @return string
     */
//    private function getAppModelName($model)
//    {
//        $model_name_parts = explode('_', $model);
//        $appModelName = '';
//        while($part = array_shift($model_name_parts) != null)
//        {
//            $appModelName .= ucfirst($part);
//        }
//        return $appModelName;
//    }

}
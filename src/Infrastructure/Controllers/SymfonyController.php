<?php

namespace Osds\Api\Infrastructure\Controllers;

use Osds\Api\Application\Get\GetEntityQueryBus;

use Illuminate\Http\Request;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Response;

use Symfony\Component\Routing\Annotation\Route;
use Nelmio\ApiDocBundle\Annotation\Model;
use Nelmio\ApiDocBundle\Annotation\Security;
use Swagger\Annotations as SWG;

// use Psr\Log\LoggerInterface;

/**
 * @Route("/api/{entity}")
 */
class SymfonyController extends BaseController {

    public $services;

    public function __construct(
        GetEntityQueryBus $query_bus,
        Request $request,
        EntityManagerInterface $entity_manager
//        LoggerInterface $logger,
//        AwsS3Util $awss3util,
//        SimpleEmailServiceClient $awsSes,
//        AwsSnsUtil $awsSns
    )
    {
        $this->services = [
            'entity_manager' => $entity_manager,
            'query_bus' => $query_bus
//            'logger' => $logger,
//            'awss3util' => $awss3util,
//            'awsSes' => $awsSes,
//            'awsSns' => $awsSns
        ];

            $_SESSION['services'] = $this->services;

        parent::__construct($request);
    }

    /**
     * @Route(
     *     "/",
     *     methods={"POST"},
     * )
     *
     * Inserts an item
     *
     * Inserts an item for the requested entity
     *
     * @SWG\Parameter(
     *     name="{entity_field}[]",
     *     in="formData",
     *     type="string",
     *     required=true,
     *     description="Each of the different fields of the entity",
     * )
     * @SWG\Response(
     *     response=200,
     *     description="Returns the id of the inserted item",
     *     )
     * )
     * @SWG\Tag(name="insert")
     * @Security(name="Bearer")
     */
    public function insert($entity)
    {
        return $this->handle('upsert', $entity);
    }


    /**
     * @Route(
     *     "/{id}",
     *     methods={"POST"},
     *     requirements={"id"="\d+"}
     * )
     *
     * Updates an item for the requested entity
     *
     * @SWG\Parameter(
     *     name="{entity_field}[]",
     *     in="formData",
     *     type="string",
     *     required=true,
     *     description="Each of the different fields of the entity",
     * )
     *
     * @SWG\Parameter(
     *     name="id",
     *     in="path",
     *     type="string",
     *     required=true,
     *     description="ID of the item to update"
     * )
     * @SWG\Response(
     *     response=200,
     *     description="Returns the id of the updated item",
     *     )
     * )
     * @SWG\Tag(name="update")
     * @Security(name="Bearer")
     */
    public function update($entity, $id = null)
    {
        return $this->handle('upsert', $entity, $id);
    }

    /**
     * @Route(
     *     "/{id}",
     *     methods={"DELETE"},
     *     requirements={"id"="\d+"}
     * )
     *
     * Deletes an item from an entity
     *
     * No furter explanation, delete from entity where id=$id
     *
     * @SWG\Parameter(
     *     name="id",
     *     in="path",
     *     type="string",
     *     description="ID of the item to delete"
     * )
     * @SWG\Response(
     *     response=200,
     *     description="Returns the id of the deleted item",
     *     )
     * )
     * @SWG\Tag(name="delete")
     * @Security(name="Bearer")
     */
    public function delete($entity, $id = null)
    {
        return $this->handle(__FUNCTION__, $entity, $id);
    }

    /**
     * @Route(
     *     "/schema",
     *     methods={"GET"}
     * )
     */
    public function getSchema($entity, $id = null)
    {
        return $this->handle(__FUNCTION__, $entity, $id);
    }

    /**
     * @Route(
     *     "/getMetadata",
     *     methods={"GET"}
     * )
     */
    public function getMetadata($entity, $id = null)
    {
        return $this->handle(__FUNCTION__, $entity, $id);
    }


    private function handle($action, $entity, $id = null)
    {
        #we make it like this to reuse the magic __call parent method that can be used with Laravel
        $uri_params[] = $entity;
        $uri_params[] = $id;
        return $this->generateResponse(parent::__call($action, $uri_params), $action);
    }

    public function generateResponse($data, $action = null)
    {

        #assigned to have it on the destruct()
        $this->response = $data;
        // All actions except 'get' do not return 'items schema' => Generate it!!
        if($action != 'get'){
            return JsonResponse::fromJsonString(json_encode($this->prepareResponseByItems($data)));
        } else {
            return JsonResponse::fromJsonString(json_encode($data));
        }
    }

}
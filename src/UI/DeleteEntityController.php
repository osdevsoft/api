<?php

namespace Osds\Api\UI;

use Illuminate\Http\Request;
use Osds\Api\Application\Delete\DeleteEntityCommand;
use Osds\Api\Application\Delete\DeleteEntityCommandBus;
use Symfony\Component\Routing\Annotation\Route;
use Nelmio\ApiDocBundle\Annotation\Model;
use Nelmio\ApiDocBundle\Annotation\Security;
use Swagger\Annotations as SWG;

/**
 * @Route("/api/{entity}")
 */
class DeleteEntityController extends BaseUIController
{

    protected $request;
    private $command_bus;

    public function __construct(
        Request $request,
        DeleteEntityCommandBus $command_bus
    )
    {
        $this->request = $request;
        $this->command_bus = $command_bus;
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

    public function handle($entity, $id = null)
    {
        $this->build($this->request);

        $message_object = $this->getEntityMessageObject($entity, $id);

        $result = $this->command_bus->dispatch($message_object);

        return $this->generateResponse($result);
    }

    public function getEntityMessageObject($entity, $id = null)
    {
        return new DeleteEntityCommand(
            $entity,
            $id
        );

    }
}
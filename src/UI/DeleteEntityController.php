<?php

namespace Osds\Api\UI;

use Illuminate\Http\Request;
use Osds\Api\Application\Delete\DeleteEntityCommand;
use Osds\Api\Domain\Bus\Command\CommandBus;
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
    private $commandBus;

    public function __construct(
        Request $request,
        CommandBus $commandBus
    )
    {
        $this->request = $request;
        $this->commandBus = $commandBus;
    }

    /**
     * @Route(
     *     "/{id}",
     *     methods={"DELETE"}
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

        $messageObject = $this->getEntityMessageObject($entity, $id);
        $messageObject->setQueue('delete_queue');

        $result = $this->commandBus->dispatch($messageObject);

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
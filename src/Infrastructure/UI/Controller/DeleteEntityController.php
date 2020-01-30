<?php

namespace Osds\Api\Infrastructure\UI\Controller;

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

    private $commandBus;

    public function __construct(
        CommandBus $commandBus
    ) {
        $this->commandBus = $commandBus;
    }

    /**
     * @Route(
     *     "/{uuid}",
     *     methods={"DELETE"}
     * )
     *
     * Deletes an item from an entity
     *
     * No furter explanation, delete from entity where id=$id
     *
     * @SWG\Parameter(
     *     name="Authorization",
     *     in="header",
     *     required=true,
     *     type="string",
     *     default="Bearer $token",
     *     description="Authorization"
     * )
     * @SWG\Parameter(
     *     name="entity",
     *     in="path",
     *     type="string",
     *     description="Entity to find in"
     * )
     * @SWG\Parameter(
     *     name="uuid",
     *     in="path",
     *     type="string",
     *     description="Returns the entity with the UUID specified. It's equivalent to '<i>search_fields[uuid]=$uuid</i>'. All previous parameters can be applied normally"
     * )
     * @SWG\Response(
     *     response=200,
     *     description="Returns the id of the deleted item",
     *     )
     * )
     * @SWG\Tag(name="delete")
     * @Security(name="Bearer")
     *
     * @return \Symfony\Component\HttpFoundation\JsonResponse
     */

    public function handle($entity, $uuid = null)
    {
        $requestParameters = $this->build();

        $messageObject = $this->getEntityMessageObject($entity, $uuid);
//        $messageObject->setQueue('delete');

        $result = $this->commandBus->dispatch($messageObject);

        return $this->generateResponse($result);
    }

    public function getEntityMessageObject($entity, $uuid = null)
    {
        return new DeleteEntityCommand(
            $entity,
            $uuid
        );
    }
}

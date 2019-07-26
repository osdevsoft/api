<?php

namespace Osds\Api\Infrastructure\UI\Controller;

use Illuminate\Http\Request;

use Osds\Api\Application\Update\UpdateEntityCommand;

use Osds\Api\Domain\Bus\Command\CommandBus;
use Symfony\Component\Routing\Annotation\Route;
use Nelmio\ApiDocBundle\Annotation\Model;
use Nelmio\ApiDocBundle\Annotation\Security;
use Swagger\Annotations as SWG;

/**
 * @Route("/api/{entity}")
 */
class UpdateEntityController extends BaseUIController
{

    protected $request;
    private $commandBus;

    public function __construct(
        Request $request,
        CommandBus $commandBus
    ) {
        $this->request = $request;
        $this->commandBus = $commandBus;
    }

    /**
     * @Route(
     *     "/{uuid}",
     *     methods={"POST"},
     * )
     *
     * Updates an item
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
     * @SWG\Response(
     *     response=200,
     *     description="Returns the id of the Updateed item",
     *     )
     * )
     * @SWG\Tag(name="update")
     * @Security(name="Bearer")
     *
     * @param $entity
     * @param $uuid
     * @return \Symfony\Component\HttpFoundation\JsonResponse
     */

    public function handle($entity, $uuid)
    {
        $this->build($this->request);

        $messageObject = $this->getEntityMessageObject($entity, $uuid, $this->request);
        $messageObject->setQueue('update');

        $result = $this->commandBus->dispatch($messageObject);

        return $this->generateResponse($result);
    }

    public function getEntityMessageObject($entity, $uuid, $request)
    {

        $data = $request->parameters;

        return new UpdateEntityCommand(
            $entity,
            $uuid,
            $data
        );
    }
}
<?php

namespace Osds\Api\UI\Controller;

use Illuminate\Http\Request;

use Osds\Api\Application\Insert\InsertEntityCommand;

use Osds\Api\Domain\Bus\Command\CommandBus;
use Osds\Api\Domain\ValueObject\Uuid;
use Osds\Api\Infrastructure\Log\LoggerInterface;
use Symfony\Component\Routing\Annotation\Route;
use Nelmio\ApiDocBundle\Annotation\Model;
use Nelmio\ApiDocBundle\Annotation\Security;
use Swagger\Annotations as SWG;

/**
 * @Route("/api/{entity}")
 */
class InsertEntityController extends BaseUIController
{

    protected $request;
    private $commandBus;
    private $logger;

    public function __construct(
        Request $request,
        CommandBus $commandBus,
        LoggerInterface $logger
    ) {
        $this->request = $request;
        $this->commandBus = $commandBus;
        $this->logger = $logger;
    }

    /**
     * @Route(
     *     "",
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
     *
     * @param $entity
     * @return \Symfony\Component\HttpFoundation\JsonResponse
     */

    public function handle($entity)
    {
        $messageObject = '';
        try {
            $this->build($this->request);

            $messageObject = $this->getEntityMessageObject($entity, $this->request);
            $messageObject->setQueue('insert');

            $result = $this->commandBus->dispatch($messageObject);

            return $this->generateResponse($result);
        } catch (\Exception $e) {
            $this->logger->error(
                'Error during the insertion. Entity: ' . $entity.', data: '. $this->request
            );
        }
    }

    public function getEntityMessageObject($entity, $request)
    {

        $uuid = Uuid::random();

        $data = $request->parameters;

        return new InsertEntityCommand(
            $entity,
            $uuid,
            $data
        );
    }
}

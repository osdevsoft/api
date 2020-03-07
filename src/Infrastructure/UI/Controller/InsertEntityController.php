<?php

namespace Osds\Api\Infrastructure\UI\Controller;

use Symfony\Component\Routing\Annotation\Route;
use Nelmio\ApiDocBundle\Annotation\Model;
use Nelmio\ApiDocBundle\Annotation\Security;
use Swagger\Annotations as SWG;

use Osds\Api\Domain\Bus\Command\CommandBus;
use Osds\DDDCommon\Infrastructure\Log\LoggerInterface;

use Osds\Api\Application\Insert\InsertEntityCommand;
use Osds\Api\Domain\ValueObject\Uuid;

/**
 * @Route("/api/{entity}")
 */
class InsertEntityController extends BaseUIController
{

    private $commandBus;
    private $logger;

    public function __construct(
        CommandBus $commandBus,
        LoggerInterface $logger
    ) {
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
     * @return \Symfony\Component\HttpFoundation\JsonResponse
     */

    public function handle($entity)
    {
        $messageObject = '';
        try {
            $requestParameters = $this->build();
            if(is_object($this->tokenValidation) && strstr(get_class($this->tokenValidation), 'JsonResponse')) {
                #Ooops!
                return $this->tokenValidation;
            }
            
            $messageObject = $this->getEntityMessageObject($entity, $requestParameters['post']);
//            $messageObject->setQueue('insert');

            $result = $this->commandBus->dispatch($messageObject);

            $return = $this->prepareResponseByItems(['upsert_id' => $result]);
        } catch (\Exception $e) {
            $message = 'Error during the insertion. Error: ' . $e->getMessage() . ', Entity: ' . $entity.', data: '. json_encode($requestParameters);
            $this->logger->error($message);
            $return = $this->prepareResponseByItems(['error_message' => $message]);
            
        }
        return $this->generateResponse($return);
    }

    public function getEntityMessageObject($entity, $requestParameters)
    {

        $uuid = Uuid::random();

        return new InsertEntityCommand(
            $entity,
            $uuid,
            $requestParameters
        );
    }
}

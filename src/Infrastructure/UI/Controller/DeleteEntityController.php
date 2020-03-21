<?php

namespace Osds\Api\Infrastructure\UI\Controller;

use Osds\Api\Application\Delete\DeleteEntityCommand;
use Osds\Api\Domain\Bus\Command\CommandBus;
use Osds\Api\Domain\Exception\ItemNotFoundException;
use Osds\DDDCommon\Infrastructure\Log\LoggerInterface;
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
    /**
     * @var LoggerInterface
     */
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
        try {

            $requestParameters = $this->build();
            if(is_object($this->tokenValidation) && strstr(get_class($this->tokenValidation), 'JsonResponse')) {
                #Ooops!
                return $this->tokenValidation;
            }

            $messageObject = $this->getEntityMessageObject($entity, $uuid);
    //        $messageObject->setQueue('delete');

            $result = $this->commandBus->dispatch($messageObject);

        } catch (ItemNotFoundException $e) {
            $e->setLogger($this->logger);
            $e->setMessage($entity, json_encode($messageObject));
            $result = $e->getResponse();
        } catch (\Exception $e) {
            $exception = new ErrorException();
            $exception->setLogger($this->logger);
            $exception->setMessage('Server Error', $e);
            $result = $exception->getResponse();
        }

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

<?php

namespace Osds\Api\Infrastructure\UI\Controller;

use Illuminate\Http\Request;

use Osds\Api\Domain\Bus\Query\QueryBus;
use Osds\Api\Application\Find\FindEntityQuery;

use Osds\Api\Domain\Exception\ErrorException;
use Osds\Api\Domain\Exception\ItemNotFoundException;

use Osds\Api\Infrastructure\Log\LoggerInterface;

use Osds\Auth\Infrastructure\UI\StaticClass\Auth;
use Symfony\Component\Routing\Annotation\Route;
use Nelmio\ApiDocBundle\Annotation\Model;
use Nelmio\ApiDocBundle\Annotation\Security;
use Swagger\Annotations as SWG;

/**
 * @Route("/api/{entity}")
 */
class FindEntityController extends BaseUIController
{

    protected $request;
    private $queryBus;
    private $logger;

    public function __construct(
        Request $request,
        QueryBus $queryBus,
        LoggerInterface $logger

    ) {
        $this->request = $request;
        $this->queryBus = $queryBus;
        $this->logger = $logger;
    }

    /**
     *
     * @Route(
     *     "/{uuid}",
     *     methods={"GET"},
     *     requirements={"uuid"="[-0-9a-z]+"}
     * )
     *
     *
     * Returns a [filtered] list of the items of an entity
     *
     * This is a common "get" action. It can be filtered by the following parameters:
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
     * @SWG\Parameter(
     *     name="referenced_entities",
     *     in="query",
     *     type="string",
     *     description="<u>Which referenced entities we want to gather</u><br>Example: <i>&referenced_entities=subentity1,subentity1.subsubentity2,subentity3</i><br>For each of the items gatherered, it will return a new 'referenced' field with the referenced models<br>Note: it will always return the 'note' reference"
     * )
     * @SWG\Parameter(
     *     name="referenced_entities_contents",
     *     in="query",
     *     type="string",
     *     description="<u>Which referenced entities we want to get all their items</u><br>Example: <i>&referenced_entities_contents=entity1,entity2</i>"
     * )
     * @SWG\Parameter(
     *     name="get_referenced_entities",
     *     in="query",
     *     type="string",
     *     description="<u>Gets the referenced entities with this entity</u><br>Example: <i>&get_referenced_entities=true</i>"
     * )
     * @SWG\Response(
     *     response=200,
     *     description="Returns a list of items for the required entity",
     *     )
     * )
     *
     * @SWG\Tag(name="find")
     * @Security(name="Bearer")
     *
     * @return \Symfony\Component\HttpFoundation\JsonResponse
     */

    public function handle($entity, $uuid)
    {
        dd(Auth::authenticate('xavicx@hotmail.com', 1234));


        $result = '';
        try {
            $this->build($this->request);

            $messageObject = $this->getEntityMessageObject($entity, $this->request, $uuid);

            $result = $this->queryBus->ask($messageObject);

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


    public function getEntityMessageObject($entity, $request, $uuid)
    {
        $request->parameters['search_fields'] = ['uuid' => $uuid];

        $searchFields = $this->getSearchFields($request);
        $queryFilters = $this->getQueryFilters($request);
        $additionalRequests = $this->getAdditionalRequests($request);

        return new FindEntityQuery(
            $entity,
            $searchFields,
            $queryFilters,
            $additionalRequests
        );
    }

    /**
     * @param $request
     * @return array
     */
    private function getSearchFields($request)
    {
        $searchFields = [];

        #we are filtering by something received from the external request
        if (isset($request->parameters['search_fields'])) {
            $searchFields += $request->parameters['search_fields'];
            #we don't need them anymore
            unset($request->parameters['search_fields']);
        }
        return $searchFields;
    }

    /**
     * @param $request
     * @return array
     */
    private function getQueryFilters($request)
    {
        $queryFilters = [];

        #we are filtering the result query
        if (isset($request->parameters['query_filters'])) {
            $queryFilters += $request->parameters['query_filters'];
            #we don't need them anymore
            unset($request->parameters['query_filters']);
        }
        return $queryFilters;
    }

    private function getAdditionalRequests($request)
    {
        $possibleAdditionalRequestsParameters = [
            'referenced_entities',
            'referenced_entities_contents',
            'get_referenced_entities'
        ];
        $additionalRequests = [];

        foreach ($possibleAdditionalRequestsParameters as $additionalRequestsParameter) {
            if (isset($request->parameters[$additionalRequestsParameter])) {
                $additionalRequests[$additionalRequestsParameter] = $request->parameters[$additionalRequestsParameter];
            }
        }

        return $additionalRequests;
    }

}
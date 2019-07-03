<?php

namespace Osds\Api\Infrastructure\UI\Controller;

use Illuminate\Http\Request;

use Osds\Api\Domain\Bus\Query\QueryBus;
use Osds\Api\Application\Search\SearchEntityQuery;

use Osds\Api\Domain\Exception\ErrorException;
use Osds\Api\Domain\Exception\ItemNotFoundException;

use Osds\Api\Infrastructure\Log\LoggerInterface;
use Symfony\Component\Routing\Annotation\Route;
use Nelmio\ApiDocBundle\Annotation\Model;
use Nelmio\ApiDocBundle\Annotation\Security;
use Swagger\Annotations as SWG;

/**
 * @Route("/api/{entity}")
 */
class SearchEntityController extends BaseUIController
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
     *     "/",
     *     methods={"GET"},
     * )
     *
     *
     * Returns a [filtered] list of the items of an entity
     *
     * This is a common "get" action. It can be filtered by the following parameters:
     *
     * @SWG\Parameter(
     *     name="search_fields",
     *     in="query",
     *     type="string",
     *     description="<u>Fields of the entity we want to filter by</u> <ul><li><b>Simple</b>: Adds a 'WHERE $fieldname=$value' filter<ul><li><i>search_fields[$fielname]=$value</i></li></ul></li></ul><ul><li><b>Complex</b> : Adds a 'WHERE $fieldname $operand $value' filter<ul><li><i>search_fields[$fielname][value]=$value&search_fields[$fielname][operand]=$operand</i> . Operand can be IN, LIKE</li></ul></li></ul>"
     * )
     * @SWG\Parameter(
     *     name="query_filter",
     *     in="query",
     *     type="string",
     *     description="<u>Filters we want to apply to the query</u> <ul><li><b>Sorting</b>: Order the results<ul><li><i>query_filter[sortby][$i][field]</i> . Field we want to sort by</li><li><i>query_filter[sortby][$i][dir]</i> . Direction we want this field to sort (ASC / DESC)</li></ul></li></ul><ul><li><b>Pagination</b>: Paginates the results<ul><li>query_filters[page_items]=n . n is number of results to retrieve</li><li><i>query_filters[page]=i</i> . i marks the initial index we want to start returning from. If not set, defaults to 1/li><li>Generated limit would be: LIMIT $page_items, $page-1 * $page_items</li></ul>"
     * )
     * @SWG\Parameter(
     *     name="referenced_entities",
     *     in="query",
     *     type="string",
     *     description="<u>Which referenced entities we want to gather</u><br>Example: <i>&referenced_entities=subentity1,subentity1.subsubentity2,entity3</i><br>For each of the items gatherered, it will return a new 'referenced' field with the referenced models<br>Note: it will always return the 'note' reference"
     * )
     * @SWG\Parameter(
     *     name="referenced_entities_contents",
     *     in="query",
     *     type="string",
     *     description="<u>Which referenced entities we want to get all their items</u><br>Example: <i>&referenced_entities_contents=entity1,entity2</i>"
     * )
     * @SWG\Parameter(
     *     name="uuid",
     *     in="path",
     *     type="string",
     *     description="Returns the entity with the UUID specified. It's equivalent to '<i>search_fields[uuid]=$uuid</i>'. All previous parameters can be applied normally"
     * )
     * @SWG\Response(
     *     response=200,
     *     description="Returns a list of items for the required entity",
     *     )
     * )
     * @SWG\Tag(name="search")
     * @Security(name="Bearer")
     *
     * @param $entity
     * @param null $uuid
     * @return \Symfony\Component\HttpFoundation\JsonResponse
     */

    public function handle($entity, $uuid = null)
    {
        $result = '';
        try {
            $this->build($this->request);

            $messageObject = $this->getEntityMessageObject($entity, $this->request, $uuid);

            $result = $this->queryBus->ask($messageObject);
        } catch (\Exception $e) {
            $exception = new ErrorException();
            $exception->setLogger($this->logger);
            $exception->setMessage('Server Error', $e);
            $result = $exception->getResponse();
        }

        return $this->generateResponse($result);
    }

    public function getEntityMessageObject($entity, $request, $uuid = null)
    {
        $searchFields = $this->getSearchFields($request);
        $queryFilters = $this->getQueryFilters($request);
        $additionalRequests = $this->getAdditionalRequests($request);

        return new SearchEntityQuery(
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
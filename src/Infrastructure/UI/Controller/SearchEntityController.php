<?php

namespace Osds\Api\Infrastructure\UI\Controller;

use Symfony\Component\Routing\Annotation\Route;
use Nelmio\ApiDocBundle\Annotation\Model;
use Nelmio\ApiDocBundle\Annotation\Security;

use Osds\Api\Domain\Bus\Query\QueryBus;
use Osds\DDDCommon\Infrastructure\Log\LoggerInterface;

use Osds\Api\Application\Search\SearchEntityQuery;
use Osds\Api\Domain\Exception\ErrorException;
use Osds\Api\Domain\Exception\ItemNotFoundException;


use Swagger\Annotations as SWG;

/**
 * @Route("/api/{entity}")
 */
class SearchEntityController extends BaseUIController
{

    private $queryBus;
    protected $logger;

    public function __construct(
        QueryBus $queryBus,
        LoggerInterface $logger

    ) {
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
     *     description="Entity to search in"
     * )
     * @SWG\Parameter(
     *     name="search_fields[]",
     *     in="query",
     *     type="string",
     *     description="<u>Fields of the entity we want to filter by</u> <ul><li><b>Simple</b>: Adds a 'WHERE $fieldname=$value' filter<ul><li><i>search_fields[$fieldname]=$value</i></li></ul></li></ul><ul><li><b>Complex</b> : Adds a 'WHERE $fieldname $operand $value' filter<ul><li><i>search_fields[$fieldname][value]=$value&search_fields[$fieldname][operand]=$operand</i> . Operand can be IN, LIKE</li></ul></li></ul>"
     * )
     * @SWG\Parameter(
     *     name="query_filter[]",
     *     in="query",
     *     type="string",
     *     description="<u>Filters we want to apply to the query</u> <ul><li><b>Sorting</b>: Order the results<ul><li><i>query_filter[sortby][$i][field]</i> . Field we want to sort by</li><li><i>query_filter[sortby][$i][dir]</i> . Direction we want this field to sort (ASC / DESC)</li></ul></li></ul><ul><li><b>Pagination</b>: Paginates the results<ul><li>query_filter[page_items]=n . n is number of results to retrieve</li><li><i>query_filter[page]=i</i> . i marks the initial index we want to start returning from. If not set, defaults to 1/li><li>Generated limit would be: LIMIT $page_items, $page-1 * $page_items</li></ul>"
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
     * @SWG\Tag(name="search")
     * @Security(name="Bearer")
     *
     * @return \Symfony\Component\HttpFoundation\JsonResponse
     */

    public function handle($entity)
    {
        $result = '';
        try {
            $requestParameters = $this->build();
            if(is_object($this->tokenValidation) && strstr(get_class($this->tokenValidation), 'JsonResponse')) {
                #Ooops!
                return $this->tokenValidation;
            }

            $messageObject = $this->getEntityMessageObject($entity, $requestParameters['get']);
            $result = $this->queryBus->ask($messageObject);
        } catch (\Exception $e) {
            $exception = new ErrorException();
            $exception->setLogger($this->logger);
            $exception->setMessage('Server Error', $e);
            $result = $exception->getResponse();
        }
        return $this->generateResponse($result);
    }

    public function getEntityMessageObject($entity, $requestParameters)
    {
        $searchFields = $this->getSearchFields($requestParameters);
        $queryFilters = $this->getQueryFilters($requestParameters);
        $additionalRequests = $this->getAdditionalRequests($requestParameters);

        return new SearchEntityQuery(
            $entity,
            $searchFields,
            $queryFilters,
            $additionalRequests
        );
    }

    /**
     * @param $requestParameters
     * @return array
     */
    private function getSearchFields($requestParameters)
    {
        $searchFields = [];

        #we are filtering by something received from the external request
        if (isset($requestParameters['search_fields'])) {
            $searchFields += $requestParameters['search_fields'];
            #we don't need them anymore
//            unset($requestParameters['search_fields']);
        }
        return $searchFields;
    }

    /**
     * @param $requestParameters
     * @return array
     */
    private function getQueryFilters($requestParameters)
    {
        $queryFilters = [];

        #we are filtering the result query
        if (isset($requestParameters['query_filters'])) {
            $queryFilters += $requestParameters['query_filters'];
            #we don't need them anymore
//            unset($requestParameters['query_filters']);
        }
        return $queryFilters;
    }

    private function getAdditionalRequests($requestParameters)
    {
        $possibleAdditionalRequestsParameters = [
            'referenced_entities',
            'referenced_entities_contents',
            'get_referenced_entities'
        ];
        $additionalRequests = [];

        foreach ($possibleAdditionalRequestsParameters as $additionalRequestsParameter) {
            if (isset($requestParameters[$additionalRequestsParameter])) {
                $additionalRequests[$additionalRequestsParameter] = $requestParameters[$additionalRequestsParameter];
            }
        }

        return $additionalRequests;
    }

}
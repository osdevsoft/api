<?php

namespace Osds\Api\UI;

use Illuminate\Http\Request;

use Osds\Api\Application\Search\SearchEntityQuery;
use Osds\Api\Application\Search\SearchEntityQueryBus;

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
    private $query_bus;

    public function __construct(
        Request $request,
        SearchEntityQueryBus $query_bus

    )
    {
        $this->request = $request;
        $this->query_bus = $query_bus;
    }

    /**
     *
     * @Route(
     *     "/",
     *     methods={"GET"},
     * )
     * @Route(
     *     "/{uuid}",
     *     methods={"GET"}
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
     *     description="<u>Fields of the entity we want to filter by</u> <ul><li><b>Simple</b>: Adds a 'WHERE $fieldname=$value' filter<ul><li><i>search_fields[$fielname]=$value</i></li></ul></li></ul><ul><li><b>Complex</b> : Adds a 'WHERE $fieldname $operand $value' filter<ul><li><i>search_fields[$fielname]['value']=$value&search_fields[$fielname]['operand']=$operand</i> . Operand can be IN, LIKE</li></ul></li></ul>"
     * )
     * @SWG\Parameter(
     *     name="query_filter",
     *     in="query",
     *     type="string",
     *     description="<u>Filters we want to apply to the query</u> <ul><li><b>Sorting</b>: Order the results<ul><li><i>query_filter['sortby'][$i]['field']</i> . Field we want to sort by</li><li><i>query_filter['sortby'][$i]['dir']</i> . Direction we want this field to sort (ASC / DESC)</li></ul></li></ul><ul><li><b>Pagination</b>: Paginates the results<ul><li>query_filters['page_items']=n . n is number of results to retrieve</li><li><i>query_filters['page']=i</i> . i marks the initial index we want to start returning from. If not set, defaults to 1/li><li>Generated limit would be: LIMIT $page_items, $page-1 * $page_items</li></ul>"
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
     *     description="Returns the entity with the UUID specified. It's equivalent to '<i>search_fields['uuid']=$uuid</i>'. All previous parameters can be applied normally"
     * )
     * @SWG\Response(
     *     response=200,
     *     description="Returns a list of items for the required entity",
     *     )
     * )
     * @SWG\Tag(name="search")
     * @Security(name="Bearer")
     */

    public function handle($entity, $uuid = null)
    {
        $this->build($this->request);

        $message_object = $this->getEntityMessageObject($entity, $this->request, $uuid);

        $result = $this->query_bus->ask($message_object);

        return $this->generateResponse($result, 'search');
    }

    public function getEntityMessageObject($entity, $request, $uuid = null)
    {
        if ($uuid != null) {
            $request->parameters = ['search_fields' => ['uuid' => $uuid]];
        }
        $search_fields = $this->getSearchFields($request);
        $query_filters = $this->getQueryFilters($request);
        $additional_requests = $this->getAdditionalRequests($request);

        return new SearchEntityQuery(
            $entity,
            $search_fields,
            $query_filters,
            $additional_requests
        );

    }

    /**
     * @param $request
     * @return array
     */
    private function getSearchFields($request)
    {
        $search_fields = [];

        #we are filtering by something received from the external request
        if (isset($request->parameters['search_fields'])) {
            $search_fields += $request->parameters['search_fields'];
            #we don't need them anymore
            unset($request->parameters['search_fields']);
        }
        return $search_fields;
    }

    /**
     * @param $request
     * @return array
     */
    private function getQueryFilters($request)
    {
        $query_filters = [];

        #we are filtering the result query
        if (isset($request->parameters['query_filters'])) {
            $query_filters += $request->parameters['query_filters'];
            #we don't need them anymore
            unset($request->parameters['query_filters']);
        }
        return $query_filters;
    }

    private function getAdditionalRequests($request)
    {
        $possible_additional_requests_parameters = ['referenced_entities', 'referenced_entities_contents'];
        $additional_requests = [];

        foreach ($possible_additional_requests_parameters as $additional_requests_parameter) {
            if (isset($request->parameters[$additional_requests_parameter])) {
                $additional_requests[$additional_requests_parameter] = $request->parameters[$additional_requests_parameter];
            }
        }

        return $additional_requests;
    }

}
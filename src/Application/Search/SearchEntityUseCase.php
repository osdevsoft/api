<?php

namespace Osds\Api\Application\Search;

final class SearchEntityUseCase
{
    private $repository;

    public function __construct(SearchEntityRepository $repository)
    {
        $this->repository = $repository;
    }

    /**
     *
     * we allow parameteres in rder to be able to be called from the same API itself
     *
     * @param string $entity :         entity we will search in
     * @param array $search_fields
     *             $search_fields[$fieldname] = value_to_search (performs a strict search (=));
     *             $search_fields[$fieldname] = [
     *                                           'value' => value_to_search,
     *                                           'operand' => 'IN' (only one supported by now)
     *                                          ]
     *
     * @param array $query_filters
     *             No sorting by default
     *             $query_filters['sortby'][$i]['field'] = $field_to_sort_by
     *             $query_filters['sortby'][$i]['dir'] = 'ASC', 'DESC'
     *
     *             $query_filters['page_items'] = n; number of results to retrieve
     *             $query_filters['page'] = i; pagination. from what index we want to start returning from (LIMIT $page_items, $page -1 * $page_items)
     *
     * @param array (from url) referenced_entities  :   Searchs the referenced contents (foreign contents) for each content of the result
     *                                                       post => in a json structure, "post" is the key of the array
     *                                                          comments => we will treat it recursively
     *                                                      author => in a json structure, "0" is the key of the array
     *
     * array entities_contents : models that we want to Search all their elements
     *
     * @return array : total_items : total number of elements (with no pagination)
     *                 items : items got
     *                 schema : schema (db fields) of the entity requested
     */
    public function execute($entity = null, $search_fields = [], $query_filters = [], $additionalRequests = [])
    {

        #retrieve the data from the database, using the specified repository
        $result_data = $this->repository->search(
            $entity,
            $search_fields,
            $query_filters
        );

        if (isset($additionalRequests['referenced_entities'])) {
            $referenced_entities = explode(',', $additionalRequests['referenced_entities']);
            $referenced_entities = $this->generateReferencedEntitiesArray($referenced_entities);
            if (!is_array($referenced_entities)) {
                $referenced_entities = [$referenced_entities];
            }

            #TODO: unhardcode note retrieval, request entity from BO
            //$referenced_entities[] = 'note';
            $result_data['items'] = $this->repository->getReferencedEntitiesContents($result_data['items'], $referenced_entities);
        } else {
            #TODO: refactor, in SearchReferencedEntitiesContents does the same
            foreach($result_data['items'] as &$item)
            {
                $item = $this->repository->convertToArray($item);
            }
        }

        if(isset($additionalRequests['referenced_entities_contents'])) {
            #we want to Search all the contents for this entities (for example, list of referenced contents on Backoffice detail)
            $Search_entities_contents = explode(',', $additionalRequests['referenced_entities_contents']);
            foreach($Search_entities_contents as $entity) {
                $this->repository->setEntity($entity);
                $result_data['required_entities_contents'][$entity] = $this->repository->retrieve($this->repository->SearchEntity());
                if(count($result_data['required_entities_contents'][$entity]['items']) > 0) {
                    $items = [];
                    foreach($result_data['required_entities_contents'][$entity]['items'] as $item) {
                        $items[] = $this->repository->convertToArray($item);
                    }
                    $result_data['required_entities_contents'][$entity] = $items;
                }
            }
        }

        $result_data['schema'] = [
            'fields' => $this->repository->getEntityFields($this->repository->getEntity())
        ];

        return $result_data;

    }


    private function generateReferencedEntitiesArray($referenced_entities) {
        $referenced_entities_array = [];
        $fn = __FUNCTION__;
        foreach ($referenced_entities as $referenced_entity) {
            if(strstr($referenced_entity, '.')) {
                // multidimensional entities request
                $referenced_entities_splited = explode('.', $referenced_entity);
                $parent = array_shift($referenced_entities_splited);
                $referenced_entities_array[$parent] = $this->{$fn}($referenced_entities_splited);
            } else {
                //if (is_array($referenced_entities_array) && count($referenced_entities_array) > 0) {
                    $referenced_entities_array[] = $referenced_entity;
                //} else {
                //    $referenced_entities_array = $referenced_entity;
                //}
            }

        }

        return $referenced_entities_array;
    }
}

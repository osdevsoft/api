<?php

namespace Osds\Api\Application\Search;

use Osds\Api\Domain\Entity\EntityRepositoryInterface;

final class SearchEntityUseCase
{
    private $repository;

    public function __construct(EntityRepositoryInterface $repository)
    {
        $this->repository = $repository;
    }

    /**
     *
     * we allow parameteres in order to be able to be called from the same API itself
     *
     * @param string $entity :         entity we will search in
     * @param array $searchFields
     *             $search_fields[$fieldname] = value_to_search (performs a strict search (=));
     *             $search_fields[$fieldname] = [
     *                                           'value' => value_to_search,
     *                                           'operand' => 'IN' (only one supported by now)
     *                                          ]
     *
     * @param array $queryFilters
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
    public function execute($entity = null, $searchFields = [], $queryFilters = [], $additionalRequests = [])
    {
        #retrieve the data from the database, using the specified repository
        $resultData = $this->repository->search(
            $entity,
            $searchFields,
            $queryFilters
        );
        $resultData['schema'] = [
            'fields' => $this->repository->getEntityData('fields')
        ];

        if (isset($additionalRequests['referenced_entities'])) {
            $referencedEntities = explode(',', $additionalRequests['referenced_entities']);
            $referencedEntities = $this->generateReferencedEntitiesArray($referencedEntities);
            if (!is_array($referencedEntities)) {
                $referencedEntities = [$referencedEntities];
            }

            $resultData['items'] = $this->repository->getReferencedEntitiesContents(
                $resultData['items'],
                $referencedEntities
            );
        } else {
            #TODO: refactor, in SearchReferencedEntitiesContents does the same
            #we do not do Query::HYDRATE_ARRAY on DoctrineRepository
            #because we want it as Entity Object to handle it more easily when handling referenced entities
            foreach ($resultData['items'] as &$item) {
                if (!is_array($item)) {
                    $item = $this->repository->convertToArray($item);
                }
            }
        }

        if (isset($additionalRequests['referenced_entities_contents'])) {
            #we want to Search all the contents for this entities
            #(for example, list of referenced contents on Backoffice detail)
            $Search_entities_contents = explode(',', $additionalRequests['referenced_entities_contents']);
            foreach ($Search_entities_contents as $entity) {
                $this->repository->setEntity($entity);
                $resultData['referenced_entities_contents'][$entity] =
                    $this->repository->search($entity);
                if (count($resultData['referenced_entities_contents'][$entity]['items']) > 0) {
                    $referencedItems = [];
                    foreach ($resultData['referenced_entities_contents'][$entity]['items'] as $referencedItem) {
                        $referencedItems[] = $this->repository->convertToArray($referencedItem);
                    }
                    $resultData['referenced_entities_contents'][$entity] = $referencedItems;
                }
            }
        }

        if ($this->repository->getEntity() == null) {
            $this->repository->setEntity($entity);
        }
        if (isset($additionalRequests['get_referenced_entities'])) {
            $resultData['referenced_entities'] = $this->repository->getReferencedEntities($this->repository->getEntity());
        }

        return $resultData;
    }

    private function generateReferencedEntitiesArray($referencedEntities)
    {
        $referencedEntitiesArray = [];
        $fn = __FUNCTION__;
        foreach ($referencedEntities as $referencedEntity) {
            if (strstr($referencedEntity, '.')) {
                // multidimensional entities request
                $referencedEntitiesSplited = explode('.', $referencedEntity);
                $parent = array_shift($referencedEntitiesSplited);
                $referencedEntitiesArray[$parent] = $this->{$fn}($referencedEntitiesSplited);
            } else {
                //if (is_array($referenced_entities_array) && count($referenced_entities_array) > 0) {
                    $referencedEntitiesArray[] = $referencedEntity;
                //} else {
                //    $referenced_entities_array = $referenced_entity;
                //}
            }

        }

        return $referencedEntitiesArray;
    }
}

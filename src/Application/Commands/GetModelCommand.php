<?php

/**
 *
 * Command that obtains data from a request with possible params
 *
 */

namespace Osds\Api\Application\Commands;

class GetModelCommand extends BaseCommand
{
    private $schemas = [];

    /**
     *
     * we allow parameteres in rder to be able to be called from the same API itself
     *
     * @param string $entity :         if not set, the one from the request will be used
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
     * @param array (from url) referenced_entities  :   gets the referenced contents (foreign contents) for each content of the result
    *                                                       post => in a json structure, "post" is the key of the array
     *                                                          comments => we will treat it recursively
     *                                                      author => in a json structure, "0" is the key of the array
     *
     * array entities_contents : models that we want to get all their elements
     *
     * @return array : total_items : total number of elements (with no pagination)
     *                 items : items got
     *                 schema : schema (db fields) of the entity requested
     */
    public function execute($entity = null, $search_fields = [], $query_filters = [])
    {
        $entity_schema = [];

        #if no entity is received, use the one on the request
        if ($entity == null) {
            $entity = $this->repository->getEntity();
        } else {
            $entity = $this->repository->setEntity($entity);
        }

        $search_fields = $this->getSearchFields($search_fields);
        $query_filters = $this->getQueryFilters($query_filters);

        #we don't want to filter by this ID anymore (remove it because we are calling this function recursively)
        unset($this->entity_id);

        #retrieve the data from the database, using the specified repository
        $result_data = $this->repository->retrieve(
            $entity,
            $search_fields,
            $query_filters
        );

        if (isset($this->request->parameters['referenced_entities'])) {
            $referenced_entities = explode(',', $this->request->parameters['referenced_entities']);
            $referenced_entities = $this->generateReferencedEntitiesArray($referenced_entities);
            if (!is_array($referenced_entities)) {
                $referenced_entities = [$referenced_entities];
            }
            $referenced_entities[] = 'note';
            $result_data['items'] = $this->repository->getReferencedEntitiesContents($result_data['items'], $referenced_entities);
        } else {
            #TODO: refactor, in getReferencedEntitiesContents does the same
            foreach($result_data['items'] as &$item)
            {
                $item = $this->repository->convertToArray($item);
            }
        }

        #TODO REFACTOR: do not hardcore "note" model, and get only if requested
//        $current_entity = strtolower($this->repository->getEntityName());
//        foreach($result_data['items'] as &$entity_item) {
//            $this->repository->setEntity('note');
//            $entity_item['references']['note'] = $this->repository->retrieve(
//                $this->repository->getEntity(),
//                [
//                    'related_model' => $current_entity,
//                    'related_model_id' => $entity_item['id'],
//                ]
//            );
//            if(count($entity_item['references']['note']['items']) > 0) {
//                $notes = [];
//                foreach($entity_item['references']['note']['items'] as $note) {
//                    $notes[] = $this->repository->convertToArray($note);
//                }
//                $entity_item['references']['note'] = $notes;
//            }
//        }

        $command = new GetSchemaModelCommand($entity);
        $result_data['schema'] = $command->execute();


        if(isset($this->request->parameters['referenced_entities_contents'])) {
            #we want to get all the contents for this entities (for example, list of referenced contents on Backoffice detail)
            $get_entities_contents = explode(',', $this->request->parameters['referenced_entities_contents']);
            foreach($get_entities_contents as $entity) {
                $this->repository->setEntity($entity);
                $result_data['required_entities_contents'][$entity] = $this->repository->retrieve($this->repository->getEntity());
                if(count($result_data['required_entities_contents'][$entity]['items']) > 0) {
                    $items = [];
                    foreach($result_data['required_entities_contents'][$entity]['items'] as $item) {
                        $items[] = $this->repository->convertToArray($item);
                    }
                    $result_data['required_entities_contents'][$entity] = $items;
                }
            }
        }

        return $result_data;
    }

    /**
     * @param $search_fields
     * @return array
     */
    private function getSearchFields($search_fields)
    {
        #if we have received an id, set the search fields to look for it
        if (isset($this->request->custom_parameters->entity_id)) {
            #we are filtering by an entry
            if (!is_array($search_fields)) $search_fields = [];
            $search_fields['uuid'] = $this->request->custom_parameters->entity_id;
        }

        #we are filtering by something recieved from the external request
        if (isset($this->request->parameters['search_fields'])) {
            if (!isset($search_fields)) $search_fields = [];
            $search_fields += $this->request->parameters['search_fields'];
            #we don't need them anymore
            unset($this->request->parameters['search_fields']);
        }
        return $search_fields;
    }

    /**
     * @param $query_filters
     * @return array
     */
    private function getQueryFilters($query_filters)
    {
        #we are filtering the result query
        if (isset($this->request->parameters['query_filters'])) {
            $query_filters += $this->request->parameters['query_filters'];
            #we don't need them anymore
            unset($this->request->parameters['query_filters']);
        }
        return $query_filters;
    }

    private function generateReferencedEntitiesArray($referenced_entities) {
        $referenced_entities_array = [];
        $fn = __FUNCTION__;
        foreach ($referenced_entities as $referenced_entity) {
            if(strstr($referenced_entity, '.')) {
                // multidimensional entities request
                $referenced_entities_splited = explode('.', $referenced_entity);
                $parent = array_shift($referenced_entities_splited);
                $referenced_entities_array[$parent][] = $this->{$fn}($referenced_entities_splited);
            } else {
                if (is_array($referenced_entities_array) && count($referenced_entities_array) > 0) {
                    $referenced_entities_array[] = $referenced_entity;
                } else {
                    $referenced_entities_array = $referenced_entity;
                }
            }
        }

        return $referenced_entities_array;
    }

}
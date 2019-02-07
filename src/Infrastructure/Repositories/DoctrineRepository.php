<?php

namespace Osds\Api\Infrastructure\Repositories;

use Doctrine\ORM\EntityManagerInterface;
use function Osds\Api\Utils\underscoreToCamelCase;

class DoctrineRepository implements BaseRepository
{

    private $entity_manager;

    const ENTITY_PATH = '\App\Entity\\';

    public function __construct(
        EntityManagerInterface $entity_manager
    )
    {
        $this->entity_manager = $entity_manager;
    }

    /**
     * @param $entity entity name
     *
     * Sets the entity to use. If a strin is received, will create it
     */
    public function setEntity($entity)
    {
        if (is_string($entity)) {
            $entity = underscoreToCamelCase($entity);
            $entity_path = self::ENTITY_PATH . $entity;
            $this->entity = new $entity_path;
        } else {
            $this->entity = $entity;
        }

    }

    /**
     * @return mixed
     */
    public function getEntity()
    {
        return $this->entity;
    }

    /*
    public function getEntityName()
    {
        return str_replace('App\Entity\\', '', get_class($this->entity));
    }
    */

    public function getEntityFQName() {
        return get_class($this->entity);
    }

    /**
     * @param $entity
     * @return array
     *
     * Given an Entity, it will return its fields
     */
    public function getEntityFields($entity)
    {
        $fields = $this->entity_manager->getClassMetadata(get_class($entity))->getColumnNames();

        return $fields;
    }

    /**
     * DEPECRATED! We expect to receive the referenced entities we want
     *
     * @param $entity
     * @return mixed
     *
     * Given an Entity, it will return the references with other entities
     */
    public function getReferencesWithOtherEntities($entity)
    {
        return $this->entity_manager->getClassMetadata(get_class($entity))->getAssociationMappings();
    }

    /**
     * @param $entity                       name of the entity we want to retrieve items
     * @param array|null $search_fields     fields we are going to filter by
     * @param array|null $query_filters     sorting, pagination
     * @return array
     */
    public function search($entity, Array $search_fields = [], Array $query_filters = [])
    {
        $this->setEntity($entity);

        #parse search fields
        $fields_to_filter_by = $this->getFieldsToFilterBy($search_fields);

        #parse sorting fields
        $fields_to_sort_by = $this->getFieldsToSortBy($query_filters);

        #parse pagination fields
        list($items_limit, $items_offset) = $this->getPaginationLimits($query_filters);

        #get repository and query builder for the queries
        $repository = $this->entity_manager->getRepository($this->getEntityFQName());

        #perform the query
        $results = $repository->findBy(
            #where
            $fields_to_filter_by,
            #sorting
            $fields_to_sort_by,
            #limit
            $items_limit,
            #offset
            $items_offset
        );

        #TODO: only get total items if required
        $total_items = $repository->count($fields_to_filter_by);

        return [
            'total_items' => isset($total_items)?$total_items:count($results),
            'items' => $results
        ];

    }

    public function insert($entity_id, $data): string
    {

        $entity = $this->getEntity();
        $repository = new $entity();

        #treat fields before updating / inserting
        foreach($data as $field => $value)
        {
            #if matches a yyyy-mm-dd, yyyy-mm-dd hh:ii, or yyyy-mm-dd hh:ii:ss
            if(preg_match('/^[0-9]{4}-[0-9]{2}-[0-9]{2}( [0-9]{2}:[0-9]{2}(:[0-9]{2})?)?$/', $value))
            {
                #add seconds to allow this type of date (yyyy-mm-dd hh:ii)
                if(preg_match('/^[0-9]{4}-[0-9]{2}-[0-9]{2} [0-9]{2}:[0-9]{2}$/', $value))
                {
                    $value .= ':00';
                }
                $value = new \DateTime($value);
            }
            $repository->{"set" . ucfirst($field)}($value);
        }

        $this->entity_manager->persist($repository);
        $result = $this->entity_manager->flush();
        return $entity_id;
    }

    public function update($entity_id, $data): string
    {

        $repository = $this->entity_manager->getRepository($this->getEntityFQName())->findBy('id', $entity_id);

        #treat fields before updating / inserting
        foreach($data as $field => $value)
        {
            #if matches a yyyy-mm-dd, yyyy-mm-dd hh:ii, or yyyy-mm-dd hh:ii:ss
            if(preg_match('/^[0-9]{4}-[0-9]{2}-[0-9]{2}( [0-9]{2}:[0-9]{2}(:[0-9]{2})?)?$/', $value))
            {
                #add seconds to allow this type of date (yyyy-mm-dd hh:ii)
                if(preg_match('/^[0-9]{4}-[0-9]{2}-[0-9]{2} [0-9]{2}:[0-9]{2}$/', $value))
                {
                    $value .= ':00';
                }
                $value = new \DateTime($value);
            }
            $repository->{"set" . ucfirst($field)}($value);
        }

        $this->entity_manager->merge($repository);
        $result = $this->entity_manager->flush();
        return $entity_id;

    }


    public function delete($entity_id) {

        $object = $this->entity_manager->getRepository($this->getEntityFQName())->find($entity_id);

        // tell Doctrine you want to (eventually) save the Product (no queries yet)
        $this->entity_manager->remove($object);

        // actually executes the queries (i.e. the INSERT query)
        $this->entity_manager->flush();

        return $entity_id;
    }

    /**
     * example of referenced_entities:
     *    post => in a json structure, "post" is the key of the array
     *        comments => we will treat it recursively
     *    author => in a json structure, "0" is the key of the array
     */
    public function getReferencedEntitiesContents(&$entity_items, $referenced_entities)
    {
        #if we want to retrieve referenced entities contents, get them
        foreach ($referenced_entities as $referenced_entity => $referenced_subentities) {

            if (!is_integer($referenced_entity)) {
                #this entity has subentities to parse
                $entity_to_gather = $referenced_entity;
            } else {
                #this array element has an integer key, it means
                $entity_to_gather = $referenced_subentities;
            }

            foreach ($entity_items as $ei_key => $entity_item) {

                $subitems = [];

                if (!isset($parsed_items[$ei_key])) {
                    $parsed_items[$ei_key] = $this->convertToArray($entity_item);
                }

                #call the method that recovers the subentity for this entity_item (from a post, get its comment entity)
                $function = "get" . underscoreToCamelCase($entity_to_gather);
                $subentity = $entity_item->{$function}();
                if (strstr(get_class($subentity), 'PersistentCollection')) {
                    #it's a One to Many relation type. Get all the items for this subentity
                    $subentity->initialize();
                    $subitems = $subentity->getSnapshot();

                } else {
                    #Many to One
                    $subentity->__load();
                    $subitems[] = $subentity;
                }

                #we have subitems for this entity and more referenced entities to parse
                if (!is_integer($referenced_entity)) {
                    $subitems = self::getReferencedEntitiesContents($subitems, $referenced_subentities);
                } else {
                    $subitems_new = [];
                    #no subentities => convert this subitems to array
                    foreach($subitems as $si)
                    {
                        $subitems_new[] = $this->convertToArray($si);
                    }
                    $subitems = $subitems_new;
                }

                $parsed_items[$ei_key]['references'][$entity_to_gather] = $subitems;

            }
        }

        return $parsed_items;
    }

    /************************/
    /*** Helper Functions ***/
    /************************/

    /**
     * @param $repository
     * @param array $search_fields
     * @return array
     */
    public function getFieldsToFilterBy(Array $search_fields): array
    {
        $fields_to_filter_by = [];



        #TODO: allow filter by fields on referenced entities

        if (!is_null($search_fields)) {
            #we want to filter by some fields
            foreach ($search_fields as $field_name => $props) {

                if (!is_array($props)) {
                    #direct seaerch
                    if (empty($props) && !is_numeric($props)) continue; # 0 is true for empty()
                    $value = $props;

                    $fields_to_filter_by[$field_name] = $value;
                } else {

                    #we have some specific filters
                    if (empty($props['value']) && !is_numeric($props['value'])) continue; # 0 is true for empty()
                    #value we are looking for

                    $value = $props['value'];

                    #if it's an array we are adding some filters. If we haven't any, by default use "LIKE"
                    if (!isset($props['operand'])) {
                        $props['operand'] = 'LIKE';
                    }

                    $operand = $props['operand'];
                    switch ($operand) {
                        #TODO: find the way to do this
//                        case 'LIKE':
//                            $value = "'%{$value}%'";
//                            break;
                        case 'IN':
                            $fields_to_filter_by[$field_name] = $value;
                    }
                }
            }
        }
        return $fields_to_filter_by;
    }

    /**
     * @param array $query_filters
     * @return array
     */
    public function getFieldsToSortBy(Array $query_filters): array
    {
        $fields_to_sort_by = [];

        if ($query_filters != null) {
            if (isset($query_filters['sortby'])) {
                for ($i = 0; $i < count($query_filters['sortby']); $i++) {
                    $field_name = $query_filters['sortby'][$i]['field'];
                    $field_direction = $query_filters['sortby'][$i]['dir'];
                    $fields_to_sort_by[$field_name] = $field_direction;
                }
            }
        }
        return $fields_to_sort_by;
    }

    /**
     * @param array $query_filters
     * @return array
     */
    public function getPaginationLimits(Array $query_filters): array
    {
        #do we really want in any case return more than 10 thousand items at a time? ¯\_(ツ)_/¯
        $items_limit = 10000;
        $items_offset = 0;
        if (isset($query_filters['page_items'])) {
            $items_limit = $query_filters['page_items'];
            if (!isset($query_filters['page'])) {
                $query_filters['page'] = 1;
            }

            $items_offset = ($query_filters['page'] - 1) * $query_filters['page_items'];
        }
        return array($items_limit, $items_offset);
    }

    /**
     * converts doctrine entity to array
     *
     * @param $entity_item
     * @return array
     */
    public function convertToArray($entity_item)
    {
        $entity_fqn = get_class($entity_item);
        $entity_fqn = str_replace('Proxies\__CG__\\', '', $entity_fqn);
        $array_entity_item = (array) $entity_item;
        foreach ($array_entity_item as $aei_key => $aei_prop) {
            unset($array_entity_item[$aei_key]);
            if(!is_object($aei_prop) && !strstr($aei_key, '__')
            ) {
                #remove null characters when doing the conversion
                $aei_key = str_replace("\0", "", $aei_key);
                $aei_key = str_replace($entity_fqn, '', $aei_key);
                $array_entity_item[$aei_key] = $aei_prop;
            }
        }

        return $array_entity_item;
    }

}

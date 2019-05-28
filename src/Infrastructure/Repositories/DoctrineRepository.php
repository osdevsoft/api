<?php

namespace Osds\Api\Infrastructure\Repositories;

use Doctrine\ORM\Query;

use function Osds\Api\Utils\underscoreToCamelCase;

class DoctrineRepository implements BaseRepository
{

    private $entity;

    private $entityManager;

    const ENTITY_PATH = '\App\Entity\\';

    public function __construct(
        $client
    )
    {
        $this->entityManager = $client;
    }

    /**
     * @param string $entity entity name
     *
     * Sets the entity to use. If a string is received, will create it
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
        $fields = $this->entityManager->getClassMetadata(get_class($entity))->getColumnNames();

        return $fields;
    }

    /**
     * @param string     $entity            name of the entity we want to retrieve items
     * @param array|null $search_fields     fields we are going to filter by
     * @param array|null $query_filters     sorting, pagination
     * @return array
     */
    public function search($entity, Array $search_fields = null, Array $query_filters = null) {
        $joined_entities = [];

        $this->setEntity($entity);

        #get repository and query builder for the queries

        $repository = $this->entityManager->getRepository($this->getEntityFQName());
        $query_builder = $repository->createQueryBuilder($entity);

        list($query_builder, $joined_entities) = $this->addFieldsToSearchBy($entity, $search_fields, $query_builder, $joined_entities);
        list($query_builder, $joined_entities) = $this->addFieldsToFilterBy($entity, $query_filters, $query_builder, $joined_entities);
        list($query_builder, $total_items) = $this->addFieldsToPaginateBy($entity, $query_filters, $query_builder);

        $items = $query_builder->getQuery()->getResult(); #Query::HYDRATE_ARRAY

        return [
            'total_items' => !is_null($total_items)?$total_items:count($items),
            'items' => $items
        ];

    }

    public function insert($entity_uuid, $data): string
    {
        $data['uuid'] = $entity_uuid;
        $entity = $this->getEntity();
        $repository = new $entity();

        #treat fields before updating / inserting
        foreach($data as $field => $value)
        {
            $value = $this->treatValuePrePersist($field, $value);
            $repository->{"set" . ucfirst($field)}($value);
        }

        $this->entityManager->persist($repository);
        $result = $this->entityManager->flush();
        return $entity_uuid;
    }

    public function update($entity_id, $data): string
    {

        $repository = $this->entityManager->getRepository($this->getEntityFQName())->find(['uuid' => $entity_id]);

        #treat fields before updating / inserting
        foreach($data as $field => $value)
        {
            $value = $this->treatValuePrePersist($field, $value);
            $repository->{"set" . ucfirst($field)}($value);
        }

        $this->entityManager->merge($repository);
        $result = $this->entityManager->flush();
        return $entity_id;

    }


    public function delete($entity_id) {

        $object = $this->entityManager->getRepository($this->getEntityFQName())->find($entity_id);
        // tell Doctrine you want to (eventually) save the Product (no queries yet)
        $this->entityManager->remove($object);

        // actually executes the queries (i.e. the INSERT query)
        $this->entityManager->flush();

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
        $parsed_items = [];

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
                    $parsed_items[$ei_key] = self::convertToArray($entity_item);
                }

                #call the method that recovers the subentity for this entity_item (from a post, get its user entity)
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
                        $subitems_new[] = self::convertToArray($si);
                    }
                    $subitems = $subitems_new;
                }
                $parsed_items[$ei_key]['references'][$entity_to_gather] = $subitems;

            }
        }

        return $parsed_items;
    }

    /************************/
    /*** Helper Functions **
     * @param $parent_entity
     * @param $search_fields
     * @param $query_builder
     * @param $joined_entities
     * @return array
     */
    /************************/

    private function addFieldsToSearchBy($parent_entity, $search_fields, $query_builder, $joined_entities)
    {
        if ($search_fields != null) {
            foreach ($search_fields as $field_name => $props) {
                $current_entity = null;
                #model to use on the where clause
                $filter_entity = $parent_entity;
                if (strstr($field_name, '.')) {
                    #this field to filter by is from another entity, not the main one
                    $entities_and_field = explode('.', $field_name);
                    $field_name = array_pop($entities_and_field);

                    $joined_entity = $parent_entity;
                    foreach($entities_and_field as $search_entity) {
                        if (!is_null($current_entity)) {
                            #first entity to join by => the other side is parent one
                            $joined_entity = $current_entity;
                        }
                        list($query_builder, $joined_entities) = $this->addEntityToQueryBuilder($joined_entity, $search_entity, $query_builder, $joined_entities);
                        $current_entity = $search_entity;
                    }
                    $filter_entity = $search_entity;
                }

                #looking for an exact match of anything else
                if (is_array($props)) {
                    if (empty($props['value']) && !is_numeric($props['value'])) continue;
                    $value = $props['value'];
                    if (isset($props['operand'])) {
                        $operand = $props['operand'];
                        switch ($operand) {
                            case 'LIKE':
                                $value = "'%{$value}%'";
                                break;
                            case 'IN':
                                if (is_array($value)) {
                                    $values = "";
                                    $fields = function ($value) use (&$values) {
                                        $is_string = false;
                                        foreach ($value as $item) {
                                            $is_string = (is_string($item));
                                        }
                                        if ($is_string) {
                                            $values = '("' . implode('","', $value) . '")';
                                        } else {
                                            $values = '(' . implode(',', $value) . ')';
                                        }
                                    };
                                    $fields($value);
                                    $value = $values;
                                }
                        }
                    } else {
                        $operand = 'LIKE';
                        $value = "'%{$value}%'";
                    }
                } else {
                    if (empty($props) && !is_numeric($props)) continue;
                    $value = (is_string($props)) ? "'{$props}'" : $props;
                    $operand = '=';
                }

                $query_builder->andWhere("{$filter_entity}.{$field_name} {$operand} {$value}");

            }
        }

        return [$query_builder, $joined_entities];
    }

    /**
     * @param $model
     * @param $field_name
     * @param $joined_entities
     * @param $query_builder
     * @param $filter_entity
     */
    private function addEntityToQueryBuilder($parent_entity, $filter_entity, $query_builder, $joined_entities): array
    {
        if(
            #we don't want to merge ourselves
            $parent_entity != $filter_entity
            #we haven't merged this entity yet
            && !in_array($filter_entity, $joined_entities)
        ) {

            $referenced_entity = '\App\Entity\\' . ucfirst($filter_entity);
            $joined_entities[] = $filter_entity;

            $referenced_entity_class_helper = new \ReflectionClass($referenced_entity);

            #check in which way we have to make the join "ON"
            $entity_field = strtolower($parent_entity) . "Uuid";
            $remote_model_field = strtolower($filter_entity)."Uuid";
            if($referenced_entity_class_helper->hasProperty($entity_field))
            {
                $origin_entity = $parent_entity;
                $origin_field = 'uuid';
                $joined_entity = $filter_entity;
                $joined_field = $entity_field;
            } else {
                $origin_entity = $filter_entity;
                $origin_field = 'uuid';
                $joined_entity = $parent_entity;
                $joined_field = $remote_model_field;
            }

            $query_builder->leftJoin(
                $referenced_entity,
                $filter_entity,
                \Doctrine\ORM\Query\Expr\Join::WITH,
                "{$origin_entity}.{$origin_field} = {$joined_entity}.{$joined_field}"
            );
        }

        return [$query_builder, $joined_entities];
    }

    private function addFieldsToFilterBy($entity, $query_filters, $query_builder, $joined_entities)
    {
        if($query_filters != null) {
            if(isset($query_filters['sortby']))
            {
                for ($i=0; $i<count($query_filters['sortby']); $i++) {
                    $field_name = $query_filters['sortby'][$i]['field'];
                    $filter_entity = $entity;
                    if (strstr($field_name, '.')) {
                        #this field to filter by is from another entity, not the main one
                        [$filter_entity, $field_name] = explode('.', $field_name);
                        list($query_builder, $joined_entities) = $this->addEntityToQueryBuilder($entity, $filter_entity, $query_builder, $joined_entities);
                    }

                    $query_builder->addOrderBy($filter_entity . '.' . $field_name, $query_filters['sortby'][$i]['dir']);
                }
            }
        }
        return [$query_builder, $joined_entities];
    }

    private function addFieldsToPaginateBy($entity, $query_filters, $query_builder)
    {

        $total_items = null;

        if (isset($query_filters['page_items'])) {
            $page_items = $query_filters['page_items'];
        } else {
            $page_items = 999;
        }

        if(!isset($query_filters['page']))
        {
            $query_filters['page'] = 1;
        }

        $start = ($query_filters['page'] - 1) * $page_items;

        $query_builder_total = clone $query_builder;
        $query_builder_total->select("count({$entity}.uuid)");
        $total_items = (int) $query_builder_total->getQuery()->getSingleScalarResult();

        $query_builder->setFirstResult($start)->setMaxResults($page_items);

        return [$query_builder, $total_items];
    }

    private function treatValuePrePersist($field, $value)
    {
        #if matches a yyyy-mm-dd, yyyy-mm-dd hh:ii, or yyyy-mm-dd hh:ii:ss
        if(is_string($value) &&
            preg_match('/^[0-9]{4}-[0-9]{2}-[0-9]{2}( [0-9]{2}:[0-9]{2}(:[0-9]{2})?)?$/', $value))
        {
            #add seconds to allow this type of date (yyyy-mm-dd hh:ii)
            if(preg_match('/^[0-9]{4}-[0-9]{2}-[0-9]{2} [0-9]{2}:[0-9]{2}$/', $value))
            {
                $value .= ':00';
            }
//            $value = new \DateTime($value);
        }

        #if another entity uuid comes, search for it to reference it
        if ($field != 'uuid' && strstr($field,'Uuid')) {
            $entity_name = str_replace('Uuid', '', $field);
            $entity_class_name = self::ENTITY_PATH . ucfirst($entity_name);
            $original_value = $value;
            $value = $this->entityManager->getRepository($entity_class_name)->find(['uuid' => $original_value]);
            if (is_null($value)) {
                throw new \Exception("$entity_name with uuid '$original_value' not found");
            }
        }

        return $value;
    }

    /**
     * converts doctrine entity to array
     *
     * @param $entity_item
     * @return array
     */
    public static function convertToArray($entity_item)
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

<?php

namespace Osds\Api\Infrastructure\Repositories;

class InMemoryRepository implements BaseRepository
{

    private $entity;

    public function setEntity($entity)
    {
        $this->entity = $entity;
    }

    public function getEntity()
    {
        return $this->entity;
    }

    public function getEntityFields()
    {
        return [];
    }

    public function insert($entity_uuid, $data)
    {
        // TODO: Implement insert() method.
    }

    public function search($entity, Array $search_fields, Array $query_filters)
    {
        $items = [];
        $totalItems = 0;

        if (count($search_fields) > 0) {
            if (isset($search_fields['uuid']) && $search_fields['uuid'] == '31415-926535-897932') {
                $totalItems = 1;
                $items[] = [
                   'uuid' => '31415-926535-897932',
                   'field' => 'value'
                ];
            }

            if (isset($search_fields['uuid']) && $search_fields['uuid'] == 'XXXXX-XXXXXX-XXXXXX') {
                // do nothing (not found)
            }

            if (isset($search_fields['profile']) && $search_fields['profile'] == 'admin') {
                $totalItems = 2;
                $items[] = [
                   'uuid' => '31415-926535-897932',
                   'field' => 'value'
                ];
                $items[] = [
                   'uuid' => '31415-926535-897932',
                   'field' => 'value'
                ];
            }
        }

        if (count($query_filters) > 0) {
            if (isset($query_filters['page']) && isset($query_filters['page_items'])) {
                $totalItems = 100;
                for ($i=0; $i<$query_filters['page_items']; $i++) {
                    $items[] = [];
                }
            }

        }

        return [
            'total_items' => $totalItems,
            'items' => $items
        ];
    }

    public function update($entityId, $data)
    {
        // TODO: Implement update() method.
    }

    public function delete($entityId)
    {
        // TODO: Implement delete() method.
    }
}

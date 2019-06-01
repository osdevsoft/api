<?php

namespace Osds\Api\Infrastructure\Repositories;

interface BaseRepository
{

    public function insert($entity_uuid, $data);

    public function search($entity, Array $search_fields, Array $query_filters);

    public function update($entityId, $data);

    public function delete($entityId);

}

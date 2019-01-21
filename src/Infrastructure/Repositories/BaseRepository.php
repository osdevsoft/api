<?php

namespace Osds\Api\Infrastructure\Repositories;

interface BaseRepository
{

    public function insert($entity_id, $data);

    public function search($entity, Array $search_fields, Array $query_filters);

    public function update($entity_id, $data);

    public function delete($entity_id);

}
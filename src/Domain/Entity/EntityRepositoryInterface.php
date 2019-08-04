<?php

namespace Osds\Api\Domain\Entity;

interface EntityRepositoryInterface
{

    public function insert($entity_uuid, $data);

    public function search($entity, Array $searchFields = null, Array $queryFilters = null);

    public function find($entity, Array $searchFields = null, Array $queryFilters = null);

    public function update($entity_uuid, $data);

    public function delete($entity_uuid);
}

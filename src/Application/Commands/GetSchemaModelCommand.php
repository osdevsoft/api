<?php

/**
 * Gets the db fields for this model and their possible relations (searched by db foreign keys) with other models
 */
namespace Osds\Api\Application\Commands;

class GetSchemaModelCommand extends BaseCommand
{

    /**
     * @return array
     */
    public function execute($entity)
    {
        $entity_data['fields'] = $this->repository->getEntityFields($entity);

        return $entity_data;
    }

}
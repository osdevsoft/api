<?php

/**
 * Command to delete a model (Eloquent soft, Doctrine hard delete)
 * No need to pass the id to delete because the parent has already stored internally the id
 */
namespace Osds\Api\Application\Commands;

class DeleteModelCommand extends BaseCommand
{

    public function execute()
    {
        $deleted_id = null;

        if($this->request->custom_parameters->entity_id != null) {
            #get by id
            $deleted_id = $this->repository->remove($this->request->custom_parameters->entity_id);
        }

        return [
            'deleted_id' => $deleted_id
        ];
    }

}
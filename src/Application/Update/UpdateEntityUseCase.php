<?php

namespace Osds\Api\Application\Update;

final class UpdateEntityUseCase
{
    private $repository;

    public function __construct(UpdateEntityRepository $repository)
    {
        $this->repository = $repository;
    }

    public function execute($entity, $id, $data)
    {
        $this->repository->setEntity($entity);
        return $this->repository->update($id, $data);
    }
}

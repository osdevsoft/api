<?php

namespace Osds\Api\Application\Delete;


final class InsertEntityUseCase
{
    private $repository;

    public function __construct(InsertEntityRepository $repository)
    {
        $this->repository = $repository;
    }

    public function execute($entity, $id)
    {
        $this->repository->setEntity($entity);
        return $this->repository->insert($data);
    }
}

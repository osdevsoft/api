<?php

namespace Osds\Api\Application\Insert;


final class InsertEntityUseCase
{
    private $repository;

    public function __construct(InsertEntityRepository $repository)
    {
        $this->repository = $repository;
    }

    public function execute($entity, $id, $data)
    {
        $this->repository->setEntity($entity);
        return $this->repository->insert($id, $data);
    }
}

<?php

namespace Osds\Api\Application\Insert;


final class InsertEntityUseCase
{
    private $repository;

    public function __construct(InsertEntityRepository $repository)
    {
        $this->repository = $repository;
    }

    public function execute($entity, $uuid, $data)
    {
        $this->repository->handler()->setEntity($entity);
        return $this->repository->handler()->insert($uuid, $data);
    }
}

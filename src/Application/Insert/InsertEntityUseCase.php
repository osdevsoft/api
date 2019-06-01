<?php

namespace Osds\Api\Application\Insert;

final class InsertEntityUseCase
{
    private $repository;

    public function __construct(InsertEntityRepository $repository)
    {
        $this->repository = $repository->handler();
    }

    public function execute($entity, $uuid, $data)
    {
        $this->repository->setEntity($entity);
        return $this->repository->insert($uuid, $data);
    }
}

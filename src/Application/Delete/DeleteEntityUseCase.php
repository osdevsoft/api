<?php

namespace Osds\Api\Application\Delete;


final class DeleteEntityUseCase
{
    private $repository;

    public function __construct(DeleteEntityRepository $repository)
    {
        $this->repository = $repository;
    }

    public function execute($entity, $uuid)
    {
        $this->repository->setEntity($entity);
        return $this->repository->delete($uuid);
    }
}

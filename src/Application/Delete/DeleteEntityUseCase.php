<?php

namespace Osds\Api\Application\Delete;

use Osds\Api\Domain\Entity\EntityRepositoryInterface;

final class DeleteEntityUseCase
{
    private $repository;

    public function __construct(EntityRepositoryInterface $repository)
    {
        $this->repository = $repository;
    }

    public function execute($entity, $uuid)
    {
        $this->repository->setEntity($entity);
        return $this->repository->delete($uuid);
    }
}

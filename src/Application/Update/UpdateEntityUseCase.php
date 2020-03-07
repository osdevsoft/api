<?php

namespace Osds\Api\Application\Update;

use Osds\Api\Domain\Entity\EntityRepositoryInterface;

final class UpdateEntityUseCase
{
    private $repository;

    public function __construct(EntityRepositoryInterface $repository)
    {
        $this->repository = $repository;
    }

    public function execute($entity, $id, $data)
    {
        $this->repository->setEntity($entity);
        return $this->repository->update($id, $data);
    }
}

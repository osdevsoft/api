<?php

namespace Osds\Api\Application\Insert;

use Osds\Api\Domain\Entity\EntityRepositoryInterface;

final class InsertEntityUseCase
{
    private $repository;

    public function __construct(EntityRepositoryInterface $repository)
    {
        $this->repository = $repository;
    }

    public function execute($entity, $uuid, $data)
    {
        $this->repository->setEntity($entity);
        return $this->repository->insert($uuid, $data);
    }
}

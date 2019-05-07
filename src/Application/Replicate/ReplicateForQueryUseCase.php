<?php

namespace Osds\Api\Application\Replicate;


final class ReplicateForQueryUseCase
{
    private $repository;

    public function __construct(ReplicateForQueryRepository $repository)
    {
        $this->repository = $repository;
    }

    public function execute($entity, $uuid, $data)
    {
        $this->repository->setEntity($entity);
        return $this->repository->insert($uuid, $data);
    }
}

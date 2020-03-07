<?php

namespace Osds\Api\Application\Replicate;

use Osds\Api\Domain\Entity\EntityRepositoryInterface;

class ReplicateForQueryUseCaseFactory
{
    private $repository;

    public function __construct(
        EntityRepositoryInterface $repository
    ) {
        $this->repository = $repository;
    }

    public function build(ReplicateForQueryCommand $command)
    {
//        $repositoryClass = str_replace('Command', 'Repository', $command->originCommand());
//        $repository = new $repositoryClass($this->repository);
        $useCaseClass = str_replace('Command', 'UseCase', $command->originCommand());
        $useCase = new $useCaseClass($this->repository);
        return $useCase;
    }
}

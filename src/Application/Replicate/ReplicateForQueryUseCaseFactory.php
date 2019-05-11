<?php

namespace Osds\Api\Application\Replicate;

use Osds\Api\Infrastructure\Repositories\BaseRepository;

class ReplicateForQueryUseCaseFactory
{
    private $repository;

    public function __construct(
        BaseRepository $repository
    )
    {
        $this->repository = $repository;
    }

    public function build(ReplicateForQueryCommand $command)
    {
        $repositoryClass = str_replace('Command', 'Repository', $command->originCommand());
        $repository = new $repositoryClass($this->repository);
        $useCaseClass = str_replace('Command', 'UseCase', $command->originCommand());
        $useCase = new $useCaseClass($repository);
        return $useCase;

    }

}
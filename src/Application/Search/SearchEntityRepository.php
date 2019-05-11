<?php

namespace Osds\Api\Application\Search;

use Osds\Api\Infrastructure\Repositories\BaseRepository;

class SearchEntityRepository
{

    private $repository;

    public function __construct(
        BaseRepository $repository
    )
    {
        $this->repository = $repository;
    }

    public function handler()
    {
        return $this->repository;
    }

}
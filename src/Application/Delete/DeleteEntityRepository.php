<?php

namespace Osds\Api\Application\Delete;

use Osds\Api\Infrastructure\Repositories\BaseRepository;

class DeleteEntityRepository
{
    private $handler;

    public function __construct(
        BaseRepository $handler
    ) {
        $this->handler = $handler;
    }

    public function handler()
    {
        return $this->handler;
    }
}

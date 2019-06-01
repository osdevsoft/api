<?php

namespace Osds\Api\Application\Update;

use Osds\Api\Infrastructure\Repositories\BaseRepository;

class UpdateEntityRepository
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
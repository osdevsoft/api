<?php

namespace Osds\Api\Application\Insert;

use Osds\Api\Infrastructure\Repositories\BaseRepository;

class InsertEntityRepository
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

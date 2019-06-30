<?php

namespace Osds\Api\Infrastructure\Persistence\Clients;

use Doctrine\ORM\EntityManagerInterface;

class DoctrineRepositoryClient implements BaseRepositoryClientInterface
{

    private $client;

    public function __construct(
        EntityManagerInterface $client
    ) {
        $this->client = $client;
    }

    public function client()
    {
        return $this->client;
    }
}

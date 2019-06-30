<?php

namespace Osds\Api\Infrastructure\Persistence\Clients;

use Elasticsearch\ClientBuilder;

class ElasticCacheRepositoryClient implements BaseRepositoryClientInterface
{

    private $client;

    public function __construct(
        ClientBuilder $client
    ) {
        $this->client = $client;
    }

    public function client()
    {
        return $this->client;
    }
}

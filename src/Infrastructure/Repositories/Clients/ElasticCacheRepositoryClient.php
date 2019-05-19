<?php

namespace Osds\Api\Infrastructure\Repositories\Clients;

use Elasticsearch\ClientBuilder;

class ElasticCacheRepositoryClient implements BaseRepositoryClientInterface
{

    private $client;

    function __construct(
        ClientBuilder $client
    )
    {
        $this->client = $client;
    }

    public function client()
    {
        return $this->client;
    }

}
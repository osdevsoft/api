<?php

namespace Osds\Api\Infrastructure\Repositories;

use Elasticsearch\ClientBuilder;

class ElasticCacheRepository implements BaseRepository
{
    private $client;
    private $entity;

    public function __construct(
        ClientBuilder $client
    ) {
        $this->client = $client::create()->setHosts(['elk'])
        ->build();
    }

    public function setEntity($entity) {

        $this->entity = $entity;
    }

    public function insert($entity_uuid, $data)
    {
//        $params = [
//            'index' => 'my_index',
//            'body' => [
//                'settings' => [
//                    'number_of_shards' => 2,
//                    'number_of_replicas' => 0
//                ]
//            ]
//        ];
//
//        $this->client->indices()->create($params);echo 'insert';
        $params = [
            'index' => $this->entity,
            'type' => 'data',
            'id' => $entity_uuid,
            'body' => $data
        ];
        $response = $this->client->index($params);
        return $response;
    }

    public function search($entity, Array $search_fields, Array $query_filters)
    {
        $params = [
            'index' => 'my_index',
            'type' => 'entity_name',
            'body' => [
                'query' => [
                    'match' => [
                        'bar' => 'foo'
                    ]
                ]
            ]
        ];

        $response = $this->client->search($params);
        return $response;
    }

    public function update($entity_uuid, $data)
    {
        $this->delete($entity_uuid);
        $this->insert($entity_uuid, $data);
    }

    public function delete($entity_uuid)
    {
        $params = [
            'index' => 'my_index',
            'type' => 'entity_name',
            'id' => 1234
        ];

        $response = $this->client->delete($params);
        return $response;
    }
}
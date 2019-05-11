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
        $this->client = $client::create()->setHosts(['elk'])->build();
    }

    public function setEntity($entity) {

        $this->entity = $entity;
    }

    public function getEntity()
    {
        return $this->entity;
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
//        $this->client->indices()->create($params);
        $params = [
            'index' => $this->entity,
            'type' => 'data',
            'id' => $entity_uuid,
            'body' => $data
        ];
        $response = $this->client->index($params);
        return $response;
    }

    public function search($entity, Array $searchFields, Array $queryFilters)
    {
        try {
            $params = [
                'index' => $entity,
                'type' => 'data',
            ];

            if (count($searchFields) > 0) {
                $params['body'] = [
                                    'query' => [
                                        'match' => $searchFields
                                    ]
                                ];
            }

            if (count($queryFilters) > 0) {

                #TODO: add mappings to index
//                if(isset($queryFilters['sortby'])) {
//                    foreach ($queryFilters['sortby'] as $key => $sortField) {
//                        $params['mappings']['properties'][$queryFilters['sortby'][$key]['field']] = ['type' => 'keyword'];
//                        $params['sort'][$queryFilters['sortby'][$key]['field']] = $queryFilters['sortby'][$key]['dir'];
//                    }
//                }

                if (isset($queryFilters['page_items'])) {
                    $pageItems = $queryFilters['page_items'];
                } else {
                    $pageItems = 999;
                }


                if(isset($queryFilters['page']))
                {
                    $pageNumber = $queryFilters['page'];
                } else {
                    $pageNumber = 1;
                }

                $start = ($pageNumber - 1) * $pageItems;

                $params['from'] = $start;
                $params['size'] = $pageItems;

            }

            $response = $this->client->search($params);

            $totalItems = @$response['hits']['total']['value'];
            $items = [];
            if ($totalItems > 0) {
               foreach($response['hits']['hits'] as $hit) {
                   $item = $hit['_source'];
                   $item['uuid'] = $hit['_id'];
                   $items[] = $item;
               }
            }

            return [
                'total_items' => $totalItems,
                'items' => $items
            ];

        } catch(\Exception $e)
        {
            dd($e);
        }
    }

    public function update($entity_uuid, $data)
    {
        $this->insert($entity_uuid, $data);
    }

    public function delete($entity_uuid)
    {
        $params = [
            'index' => $this->entity,
            'type' => 'data',
            'id' => $entity_uuid
        ];

        $response = $this->client->delete($params);
        return $response;
    }

    public function getEntityFields($entity)
    {
        return ['name', 'email'];

        $params = [
            'index' => 'EntityMetadata',
            'type' => 'data',
            'body' => [
                'query' => [
                    'match' => [
                        '_id' => $entity
                    ]
                ]
            ]
        ];
        $response = $this->client->search($params);

    }
}
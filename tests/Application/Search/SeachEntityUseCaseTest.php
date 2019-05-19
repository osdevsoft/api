<?php

namespace Osds\Api\Application\Search;

use PHPUnit\Framework\TestCase;

use Osds\Api\Infrastructure\Repositories\InMemoryRepository;

final class SearchEntityUseCaseTest extends TestCase {

    private $useCase;

    public function setUp(): void
    {
        $repository = new InMemoryRepository();
        $useCaseRepository = new SearchEntityRepository($repository);
        $this->useCase = new SearchEntityUseCase($useCaseRepository);
    }

    public function testGetOne()
    {
        $uuid = '31415-926535-897932';
        $searchFields = [
            'uuid' => $uuid
        ];
        $result = $this->useCase->execute('entity', $searchFields);

        $this->basicResponseAssertions($result);

        $this->assertEquals($result['total_items'], 1);
        $this->assertEquals($result['items'][0]['uuid'], $uuid);
    }


    public function testGetOneNotFound()
    {
        $uuid = 'XXXXX-XXXXXX-XXXXXX';
        $searchFields = [
            'uuid' => $uuid
        ];
        $result = $this->useCase->execute('entity', $searchFields);

        $this->basicResponseAssertions($result);

        $this->assertEquals($result['total_items'], 0);
    }


    public function testGetMany()
    {
        $searchFields = [
            'profile' => 'admin'
        ];
        $result = $this->useCase->execute('entity', $searchFields);

        $this->basicResponseAssertions($result);

        $this->assertGreaterThan(1, $result['total_items']);

    }

    public function testGetPaginated()
    {
        $queryFilters = [
            'page' => 2,
            'page_items' => 10
        ];
        $result = $this->useCase->execute('entity', [], $queryFilters);

        $this->basicResponseAssertions($result);

        $this->assertGreaterThan(count($result['items']), $result['total_items']);

    }

    private function basicResponseAssertions($result)
    {
        $this->assertIsNumeric($result['total_items']);
    }

}
<?php

namespace Osds\Api\UI\Controller;

use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;

class SearchEntityControllerTest extends WebTestCase
{
    public function testGetOne()
    {
        $client = static::createClient();

        $client->request('GET', '/api/post/post-1');

        $this->assertEquals(200, $client->getResponse()->getStatusCode());
    }
}
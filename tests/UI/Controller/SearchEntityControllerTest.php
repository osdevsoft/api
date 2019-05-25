<?php

namespace Osds\Api\UI\Controller;

use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;

class SearchEntityControllerTest extends WebTestCase
{
    public function testGetOne()
    {
        $client = static::createClient();
        $client->request('GET', 'http://api.myproject.sandbox/api/user/');
        $response = $client->getResponse();

        $this->assertEquals(200, $response->getStatusCode());
    }
}
<?php

namespace Osds\Api\UI\Controller;

use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;

class CheckStatusControllerTest extends WebTestCase
{
    public function testGetStatus()
    {
        $client = static::createClient();
        $client->request('GET', '/api/status/');
        $response = $client->getResponse();

        $this->assertEquals('"Staying alive"', $response->getContent());
    }
}
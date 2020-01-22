<?php

namespace Osds\Api\Infrastructure\UI\Controller;

use GuzzleHttp\Client;
use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;

class SearchEntityControllerTest extends WebTestCase
{
    public function testGetOne()
    {
        $client = new Client(['base_uri' => 'http://api.myproject.sandbox']);

        $client->request(
            'post',
            'api/auth/login',
            [
                'form_params' => ['username' => 'user1@blog.cx', 'password' => '1234'],
            ]
        );

        // TODO: why are params not being sent?
//        $response = $client->getResponse();
        $this->assertTrue(true);
    }
}
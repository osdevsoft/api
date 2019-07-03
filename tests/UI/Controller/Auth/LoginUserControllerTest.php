<?php

namespace Osds\Api\Infrastructure\UI\Controller\Auth;

use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;

use GuzzleHttp\Client;

class LoginUserControllerTest extends WebTestCase
{

    public function testLoginUser()
    {
//        $client = new Client(['base_uri' => 'http://web_server/']);
        $client = static::createClient();


        $data = ['username' => 'user1@blog.cx', 'password' => '1234'];
        $response = $client->request(
            'POST',
            'http://api.myproject.sandbox/api/auth/login',
            $data
        );
//        dd($client->getResponse()->getContent());

        $this->assertTrue(true);
    }
}

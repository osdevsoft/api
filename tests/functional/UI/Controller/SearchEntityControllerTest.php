<?php

namespace Osds\Api\Infrastructure\UI\Controller;

use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;

class SearchEntityControllerTest extends WebTestCase
{
    public function testGetOne()
    {
        $client = static::createClient();

        // Bearer eyJ0eXAiOiJKV1QiLCJhbGciOiJSUzI1NiJ9.eyJpYXQiOjE1NjAzNjA3OTksImV4cCI6MTU2MDk2NTU5OSwicm9sZXMiOltdLCJ1c2VybmFtZSI6InVzZXIxQGJsb2cuY3gifQ.mi6z8cIGJJhm6hghEBB7AlJB_EBpR9jtJoW9FyGFnEV8yhlVFaVXZrZYLHdqT2ofMIlLIUPWuPHbXhOiU_9Ez08_UVHpEpiZ3ycQdnK_7FCo45XDx8kCwlEwzj8DrzdVhJMz1VmVl77MRiT4IiMa17xP3O4OCCNZL1PkGdDuBuPG3oxDfHRyZnhVkMq5GQYnQLXmBJE1rQv6yOqxkGmr3OJDJ8DRsZev65d9KF8jAFM5woDl6DoIZp5hm2Dl6Igs97P7iBu1R5eL9x19zdTixNxE8dZZT3kFNMCVVPJ-XSLRDLBxWVrGN8mqDp9Oz9iwHPOTUd3n-4X5YVAxRbjcz1PeaTQ6CZvpzCyTb5gAEIb8eshCfycXSdI9DIR23U-T9DAy1vQ5VN13e6tF8S7knHg2nu8Cej1UIVonz6YRrr_7fXNHAwImfu_eKBDSAHIVskD8mPqrnMNtwZs1C5IG4hzOOc55m6eXW3DyNKbo9cm_SHhcUNtbArKTu5n-Bv-9MBcs4MivM8PudqN20UXBbKGwqPDTQV0VjTbahphY8IExdLU9A81l8oZC1NIGXzMz_b5glHaSA7enDhCYDbOII7PYj5ojp9HhTpUxzQPHtWUrUfTM0VgKipl4QHLQlFRutpqkWpdffOQOlxVtjMeWvBD7Udk-HHtwVRq22VwIRgw

        $client->request('GET', 'http://api.myproject.sandbox/api/user/');
        $response = $client->getResponse();

        // TO DO
        $this->assertTrue(true);
//        $this->assertEquals(200, $response->getStatusCode());
    }
}
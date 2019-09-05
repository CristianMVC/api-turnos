<?php

namespace ApiV1Bundle\Tests\Controller;

use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;

class RestTestControllerTest extends WebTestCase
{
    public function testGet()
    {
        $client = static::createClient();
        $client->followRedirects();
        $client->request('GET', '/api/v1.0/test');
        $this->assertEquals(200, $client->getResponse()->getStatusCode());
    }

    public function testPost()
    {
        $client = static::createClient();
        $client->followRedirects();
        $client->request('POST', '/api/v1.0/test');
        $this->assertEquals(200, $client->getResponse()->getStatusCode());
    }

    public function testPut()
    {
        $client = static::createClient();
        $client->followRedirects();
        $client->request('PUT', '/api/v1.0/test');
        $this->assertEquals(200, $client->getResponse()->getStatusCode());
    }

    public function testDelete()
    {
        $client = static::createClient();
        $client->followRedirects();
        $client->request('DELETE', '/api/v1.0/test');
        $this->assertEquals(200, $client->getResponse()->getStatusCode());
    }

    public function test400()
    {
        $client = static::createClient();
        $client->followRedirects();
        $client->request('GET', '/api/v1.0/test/400');
        $this->assertEquals(400, $client->getResponse()->getStatusCode());
    }

    public function test404()
    {
        $client = static::createClient();
        $client->followRedirects();
        $client->request('GET', '/api/v1.0/test/404');
        $this->assertEquals(404, $client->getResponse()->getStatusCode());
    }
}

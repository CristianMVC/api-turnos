<?php
namespace ApiV1Bundle\Tests\Controller;

class AutenticacionControllerTest extends ControllerTestCase
{
    /**
     * postAction Este método realiza un login
     * Endpoint: auth/login
     */
    public function testPostAction()
    {
        $client = static::createClient();
        $client->followRedirects();
        $params =  [
            "username" => "test",
            "password"=> "test"
        ];
        $client->request('POST', '/api/v1.0/auth/login', $params);
        $this->assertEquals(200, $client->getResponse()->getStatusCode());
    }

}
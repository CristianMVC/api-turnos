<?php
namespace ApiV1Bundle\Tests\Controller;

use ApiV1Bundle\Mocks\SNCExternalServiceMock;

/**
 * Class SecurityControllerTest
 * @package ApiV1Bundle\Tests\Controller
 */
class SecurityControllerTest extends ControllerTestCase
{
    /**
     * Test login
     * @return mixed
     */
    public function testLogin()
    {
        $client = static::createClient();
        $client->followRedirects();
        $params = [
            'username' => 'test@test.com',
            'password' => 'test'
        ];
        $client->request('POST', '/api/v1.0/auth/login', $params);
        // content
        $content = json_decode($client->getResponse()->getContent(), true);
        $this->assertEquals(200, $client->getResponse()->getStatusCode());
        $this->assertTrue(array_key_exists('id', $content));
        $this->assertTrue(array_key_exists('username', $content));
        $this->assertTrue(array_key_exists('token', $content));
        $this->assertTrue(array_key_exists('organismo', $content));
        $this->assertTrue(array_key_exists('area', $content));
        $this->assertTrue(array_key_exists('puntoAtencion', $content));
        $this->assertTrue(array_key_exists('rol', $content));
        return $content['token'];
    }

    /**
     * Test token
     * @depends testLogin
     */
    public function testToken($token)
    {
        $client = static::createClient();
        $client->followRedirects();
        $headers = [
            'HTTP_AUTHORIZATION' => "Bearer {$token}",
            'CONTENT_TYPE' => 'application/json'
        ];
        $client->request('POST', '/api/v1.0/auth/test', [], [], $headers);
        $this->assertEquals(200, $client->getResponse()->getStatusCode());
        return $token;
    }

    /**
     * Test logout user
     * @depends testToken
     */
    public function testLogout($token)
    {
        $client = static::createClient();
        $client->followRedirects();
        $headers = [
            'HTTP_AUTHORIZATION' => "Bearer {$token}",
            'CONTENT_TYPE' => 'application/json'
        ];
        $client->request('POST', '/api/v1.0/auth/logout', [], [], $headers);
        $content = json_decode($client->getResponse()->getContent(), true);
        $this->assertEquals(200, $client->getResponse()->getStatusCode());
        $this->assertTrue(array_key_exists('status', $content));
        $this->assertEquals('SUCCESS', $content['status']);
        return $token;
    }

    /**
     * Test de comunicación entre APIs
     */
    public function testSecureCommunication()
    {
        $client = static::createClient();
        $client->followRedirects();
        $params = [
            'username' => 'test@test.com',
            'password' => 'test'
        ];
        $client->request('POST', '/api/v1.0/integration/secure/request', $params);
        // sign the request
        $externalSevice = new SNCExternalServiceMock($this->getContainer());
        $signedRequest = $externalSevice->getTestSignedBody($params);
        // content
        $content = json_decode($client->getResponse()->getContent(), true);
        $this->assertEquals(200, $client->getResponse()->getStatusCode());
        $this->assertEquals($content['body']['username'], $params['username']);
        $this->assertEquals($content['body']['password'], $params['password']);
        $this->assertEquals($content['body']['api_id'], $signedRequest->api_id);
        $this->assertEquals($content['body']['signature'], $signedRequest->signature);
    }
}

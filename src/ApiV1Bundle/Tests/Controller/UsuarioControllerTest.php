<?php

namespace ApiV1Bundle\Tests\Controller;

/**
 * Class UsuarioControllerTest
 * @package ApiV1Bundle\Tests\Controller
 */
class UsuarioControllerTest extends ControllerTestCase
{
    /**
     * Recibe un nombre de usuario, obtiene un JWT y devuelve el arreglo de
     * headers
     *
     * @param $username
     * @return array
     */
    private function getHeaders($username)
    {
        $client = static::createClient();
        $client->followRedirects();
        $params = [
            'username' => $username,
            'password' => 'test'
        ];
        $client->request('POST', '/api/v1.0/auth/login', $params);
        $data = json_decode($client->getResponse()->getContent(), true);
        return [
            'HTTP_AUTHORIZATION' => "Bearer {$data['token']}"
        ];
    }

    /**
     * Obtener listado de usuarios con un usuario admin
     * testAdminGetListadoAction
     */
    public function testAdminGetListadoAction()
    {
        $headers = $this->getHeaders('admin@mail.com');
        $client = static::createClient();
        $client->followRedirects();
        $client->request('GET', '/api/v1.0/usuarios', [], [], $headers);
        self::assertEquals(200, $client->getResponse()->getStatusCode());
    }

    /**
     * Obtener listado de usuarios con un usuario organismo
     * testOrganismoGetListadoAction
     */
    public function testOrganismoGetListadoAction()
    {
        $headers = $this->getHeaders('organismo@mail.com');
        $client = static::createClient();
        $client->followRedirects();
        $client->request('GET', '/api/v1.0/usuarios', [], [], $headers);
        self::assertEquals(200, $client->getResponse()->getStatusCode());
    }

    /**
      Obtener listado de usuarios con un usuario area
     * testAreaGetListadoAction
     */
    public function testAreaGetListadoAction()
    {
        $headers = $this->getHeaders('area@mail.com');
        $client = static::createClient();
        $client->followRedirects();
        $client->request('GET', '/api/v1.0/usuarios', [], [], $headers);
        self::assertEquals(200, $client->getResponse()->getStatusCode());
    }

    /**
     * Obtener listado de usuarios con un usuario responsable de punto de atención
     * testPuntoAtencionGetListadoAction
     */
    public function testPuntoAtencionGetListadoAction()
    {
        $headers = $this->getHeaders('pda@mail.com');
        $client = static::createClient();
        $client->followRedirects();
        $client->request('GET', '/api/v1.0/usuarios', [], [], $headers);
        self::assertEquals(200, $client->getResponse()->getStatusCode());
    }
}

<?php

namespace ApiV1Bundle\Tests\Controller;

/**
 * Class UsuariosAdminControllerTest
 * @package ApiV1Bundle\Tests\Controller
 */
class UsuariosAdminControllerTest extends ControllerTestCase
{
    /**
     * @var integer $usuarioId
     */
    protected $usuarioId;

    /**
     * PostAction
     * Este método testea el alta de un nuevo usuario
     * $params Array conteniendo los datos necesarios para dar de alta un nuevo usuario
     * Endpoint: /usuarios
     * @return integer
     */
    public function testPostAction()
    {
        $client = static::createClient();
        $client->followRedirects();
        $params = [
            'nombre' => 'NombreUsuarioAdmin',
            'apellido' => 'ApellidoUsuarioAdmin',
            'username' => uniqid('user+') . '@gmail.com',
            'rol' => 1
        ];
        $client->request('POST', '/api/v1.0/usuarios', $params);
        $usuarioDatos = json_decode($client->getResponse()->getContent());

        $this->usuarioId = $usuarioDatos->additional->id;

        $this->assertEquals(200, $client->getResponse()->getStatusCode());
        return $this->usuarioId;
    }

    /**
     * GetItemAction Este método testea el usuario creado con el método postAction
     * Endpoint: usuarios/{usuarioId}
     *
     * @param Integer usuarioId Este id corresponde al nuevo usuario creado en el método postAction
     * Annotation depends permite generar una dependencia explicita entre métodos,
     * para el caso particular entre PostAction y GetItemAction
     *
     * @depends testPostAction
     */
    public function testGetItemAction($usuarioId)
    {
        $this->usuarioId = $usuarioId;
        $client       = static::createClient();
        $client->followRedirects();
        $client->request('GET', '/api/v1.0/usuarios/' . $this->usuarioId);
        $this->assertEquals(200, $client->getResponse()->getStatusCode());
    }

    /**
     * PutAction Este método testea la modificación del usuario creado con el método postAction
     * Endpoint: usuarios/{usuarioId}
     *
     * @param Integer usuarioId Este id corresponde al nuevo usuario creado en el método postAction
     * Annotation depends permite generar una dependencia explicita entre métodos,
     * para el caso particular entre testPostAction y testPutAction
     *
     * @depends testPostAction
     */
    public function testPutAction($usuarioId)
    {
        $this->usuarioId = $usuarioId;
        $client       = static::createClient();
        $client->followRedirects();
        $params = [
            'nombre' => 'Nombre2UsuarioAdmin',
            'apellido' => 'Apellido2UsuarioAdmin',
            'username' => uniqid('user+') . '@gmail.com',
            'rol' => 1
        ];
        $client->request('PUT', '/api/v1.0/usuarios/' . $this->usuarioId, $params);
        $this->assertEquals(200, $client->getResponse()->getStatusCode());
    }

    /**
     * DeleteAction Este método testea el borrado del usuario creado con el método postAction
     * Endpoint: organismos/usuarios/{usuarioId}
     *
     * @param Integer usuarioId Este id corresponde al nuevo usuario creado en el método postAction
     * Annotation depends permite generar una dependencia explicita entre métodos,
     * para el caso particular entre testPostAction y testDeleteAction
     *
     * @depends testPostAction
     */
    public function testDeleteAction($usuarioId)
    {
        $this->usuarioId = $usuarioId;
        $client = static::createClient();
        $client->followRedirects();
        $client->request('DELETE', '/api/v1.0/usuarios/' . $this->usuarioId);
        $this->assertEquals(200, $client->getResponse()->getStatusCode());
    }
}

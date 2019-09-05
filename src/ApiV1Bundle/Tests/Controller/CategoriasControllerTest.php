<?php

namespace ApiV1Bundle\Tests\Controller;

/**
 * Class CategoriasControllerTest
 * @package ApiV1Bundle\Tests\Controller
 */

class CategoriasControllerTest extends ControllerTestCase
{
    /**
     * Test para crear una categoría
     * Endpoint: /api/v1.0/puntosatencion/1/categorias
     * @return mixed
     */
    public function testPostAction()
    {
        $client = static::createClient();
        $client->followRedirects();

        $params = [
            'nombre' => 'Smoke Test Categorias',
            'tramites' => [26]
        ];

        $client->request('POST', '/api/v1.0/puntosatencion/1/categorias', $params);
        $datosCategoria = json_decode($client->getResponse()->getContent());
        $data = json_decode($client->getResponse()->getContent(), true);
        self::assertEquals(200, $client->getResponse()->getStatusCode());
        return $datosCategoria->additional->id;
    }

    /**
     * Test para modificar una categoría
     * Endpint: /api/v1.0/puntosatencion/1/categorias/{idCategoria}
     * Annotation depends permite generar una dependencia explicita entre métodos.
     * Para el caso particular entre testPostAction y testPutAction
     * @param $id
     * @depends testPostAction
     */
    public function testPutAction($id)
    {
        $client = static::createClient();
        $client->followRedirects();

        $params = [
            'nombre' => 'Smoke Test Categorias Edit',
            'tramites' => [26]
        ];

        $client->request('PUT', '/api/v1.0/puntosatencion/1/categorias/' . $id, $params);
        self::assertEquals(200, $client->getResponse()->getStatusCode());
    }

    /**
     * Test para obtener una categoría
     * Endpoint: /api/v1.0/puntosatencion/1/categorias/{idCategoria}
     * Annotation depends permite generar una dependencia explicita entre métodos.
     * Para el caso particular entre testPostAction y testGetAction
     * @depends testPostAction
     */
    public function testGetAction($id)
    {
        $client = static::createClient();
        $client->followRedirects();

        $client->request('GET', '/api/v1.0/puntosatencion/1/categorias/' . $id);
        self::assertEquals(200, $client->getResponse()->getStatusCode());
    }

    /**
     * Obtenemos un listado de categorías
     * Endpoint: /api/v1.0/puntosatencion/1/categorias
     */
    public function testGetListAction()
    {
        $client = static::createClient();
        $client->followRedirects();

        $params = [
            'offset' => 0,
            'limit' => 10
        ];

        $client->request('GET', '/api/v1.0/puntosatencion/1/categorias', $params);
        self::assertEquals(200, $client->getResponse()->getStatusCode());
    }

    /**
     * Eliminamos una categoría
     * @param $id
     * Endopoint: /api/v1.0/puntosatencion/1/categorias
     * Annotation depends permite generar una dependencia explicita entre métodos.
     * Para el caso particular entre testPostAction y testGetAction
     * @depends testPostAction
     */
    public function testDeleteAction($id)
    {
        $client = static::createClient();
        $client->followRedirects();

        $client->request('DELETE', '/api/v1.0/puntosatencion/1/categorias/' . $id);
        self::assertEquals(200, $client->getResponse()->getStatusCode());
    }

    /**
     * Tramites disponibles por categoría
     * Endopoint: /api/v1.0/puntosatencion/1/categorias/tramitesdisponibles
     */
    public function testTramitesDisponiblesAction()
    {
        $client = static::createClient();
        $client->followRedirects();

        $client->request('GET', '/api/v1.0/puntosatencion/1/categorias/tramitesdisponibles');
        self::assertEquals(200, $client->getResponse()->getStatusCode());
    }
}

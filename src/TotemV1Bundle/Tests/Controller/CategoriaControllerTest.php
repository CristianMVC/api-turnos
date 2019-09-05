<?php

namespace TotemV1Bundle\Tests\Controller;
/**
 * Class CategoriaControllerTest
 * @package TotemV1Bundle\Tests\Controller
 */
class CategoriaControllerTest extends ControllerTestCase {

    /**
     * Test listado de categorías por punto de atención
     */
    public function testGetListAction()
    {
        $client = static::createClient();
        $client->followRedirects();

        $client->request('GET', 'api/v1.0/totem/puntosatencion/1/categorias');
        $this->assertEquals(200, $client->getResponse()->getStatusCode());
    }
}

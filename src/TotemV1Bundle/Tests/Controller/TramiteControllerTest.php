<?php
namespace TotemV1Bundle\Tests\Controller;

/**
 * Class TramiteControllerTest
 * @package TotemV1Bundle\Tests\Controller
 */

class TramiteControllerTest extends ControllerTestCase
{
    /**
     * GetTramitesAction
     * Este método testea el listado trámites por punto de atención y filtrado por nombre
     * Endpoint: /totem/puntoatencion/{puntoatendionId}/tramites
     */
    public function testGetTramitesAction()
    {
        $client = static::createClient();
        $client->followRedirects();
        $params = [
            'offset' => 0,
            'limit' => 5,
            'q' => 'a'
        ];
        $client->request('GET', '/api/v1.0/totem/puntosatencion/1/tramites/buscar',$params);
        $this->assertEquals(200, $client->getResponse()->getStatusCode());
    }

    /**
     * GetTramitesAction
     * Este método testea el listado trámites por categoría
     * Endpoint: /totem/puntosatencion/{puntoAtencionId}/categorias/{categoriaId}/tramites
     */
    public function testGetTramitesCategoriaAction()
    {
        $client = static::createClient();
        $client->followRedirects();
        $params = [
            'offset' => 0,
            'limit' => 5
        ];
        $client->request('GET', '/api/v1.0/totem/puntosatencion/1/categorias/1/tramites',$params);
        $this->assertEquals(200, $client->getResponse()->getStatusCode());
    }

    /**
     * GetItemAction Este método testea el obtener un tramite que exista en la base de datos
     * Endpoint: /totem/puntosatencion/{puntoAtencionId}/tramites/{tramiteId}
     */
    public function testGetItemAction()
    {
        $client = static::createClient();
        $client->followRedirects();
        $client->request('GET', '/api/v1.0/totem/puntosatencion/1/tramites/2');
        $this->assertEquals(200, $client->getResponse()->getStatusCode());
    }
}

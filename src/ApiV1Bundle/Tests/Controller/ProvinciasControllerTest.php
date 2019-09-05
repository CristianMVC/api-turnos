<?php
namespace ApiV1Bundle\Tests\Controller;

/**
 * Class ProvinciasControllerTest
 * @package ApiV1Bundle\Tests\Controller
 */
class ProvinciasControllerTest extends ControllerTestCase
{

    /**
     * GetListAction
     * Este método testea un listado de provincias
     * Endpoint /provincias
     */
    public function testGetListAction()
    {
        $client = static::createClient();
        $client->followRedirects();
        $client->request('GET', '/api/v1.0/provincias');
        $this->assertEquals(200, $client->getResponse()->getStatusCode());
    }

    /**
     *  GetLocalidadesAction
     * Este método testea el listado de localidades de una provincia
     * $params Arrau conteniendo los datos para limitar el listado. Valores por defecto para offset y limit 0 y 10 respectivamente
     * Endpoint: /provincias/{provinciaId}/localidades
     */
    public function testGetLocalidadesAction()
    {
        $client = static::createClient();
        $client->followRedirects();
        $params = [
            'offset'=>'0',
            'limit'=>'10'
        ];
        $client->request('GET', '/api/v1.0/provincias/1/localidades', $params);
        $this->assertEquals(200, $client->getResponse()->getStatusCode());
    }

    /**
     * GetProvinciasTramiteAction
     * Este método testea el listado de provincias por trámite
     * $params Arrau conteniendo los datos para limitar el listado. Valores por defecto para offset y limit 0 y 10 respectivamente
     * Endpoint: tramites/tramiteId/provincias
     */
    public function testGetProvinciasTramiteAction()
    {
        $client = static::createClient();
        $client->followRedirects();
        $params = [
            'offset'=>'0',
            'limit'=>'10'
        ];
        $client->request('GET', '/api/v1.0/tramites/14/provincias', $params);
        $this->assertEquals(200, $client->getResponse()->getStatusCode());
    }

    /**
     * GetLocalidadesTramiteAction
     * Este método permite listar las localidades por tramiteId y provinciaId
     * $params Arrau conteniendo los datos para limitar el listado. Valores por defecto para offset y limit 0 y 10 respectivamente
     * Endpoint: tramites/{tramiteId}/provincias/{provinciaId}
     */

    public function testGetLocalidadesTramiteAction()
    {
        $client = static::createClient();
        $client->followRedirects();
        $params = [
            'offset' => '0',
            'limit'  => '10'
        ];
        $client->request('GET', '/api/v1.0/tramites/14/provincias/1', $params);

    }

    /**
     * GetLocalidadesTramiteAction
     * Este método permite buscar localidades por provincia y criterio de búsqueda
     * Endpoint: provincias/{provinciaId}/localidades/buscar
     */
    public function testBuscarLocalidades()
    {
        $client = static::createClient();
        $client->followRedirects();

        $params = [
            'qry' => 'a. '
        ];

        $client->request('GET', '/api/v1.0/provincias/1/localidades/buscar', $params);
        $this->assertEquals(200, $client->getResponse()->getStatusCode());
    }
}

<?php

namespace ApiV1Bundle\Tests\Controller;

/**
 * Class GrupoTramitesControllerTest
 * @package ApiV1Bundle\Tests\Controller
 */

class GrupoTramitesControllerTest extends ControllerTestCase
{
    /** @var integer GruposTramitesId */
    protected $gruposTramitesId;

    /**
     * GetListAction
     * Método que testea el listado de grupo de trámites
     * $params Array con los valores de offset y limit para el listado, valores por defecto offset=0, limit=10
     * Endpoint: puntosatencion/{puntoAtencionId}/grupostramites
     */

    public function testGetListAction()
    {
        $client = static::createClient();
        $client->followRedirects();
        $params = [
            'offset'=>'0',
            'limit'=>'10'
        ];
        $client->request('GET', '/api/v1.0/puntosatencion/23/grupostramites', $params);
        $this->assertEquals(200, $client->getResponse()->getStatusCode());
    }

    /**
     * PostAction
     * Este método testea el alta de un nuevo grupo de trámites
     * $params Array conteniendo los datos necesarios para dar de alta un nuevo Grupo de Tramite.
     * Endpoint: puntosatencion/{puntoAtenciónId}/grupostramites
     * @return integer
     */
    public function testPostAction()
    {
        $client = static::createClient();
        $client->followRedirects();
        $params = [
            'nombre'    => 'gp::23:01',
            'horizonte' => 30,
            'tramites'  => [
                45
            ],
            'intervalo' => 15
        ];
        $client->request('POST', '/api/v1.0/puntosatencion/1/grupostramites', $params);
        $datosGruposTramites = json_decode($client->getResponse()->getContent(), true);
        $this->gruposTramitesId = $datosGruposTramites['additional']['id'];
        $this->assertEquals(200, $client->getResponse()->getStatusCode());
        return $this->gruposTramitesId;
    }

    /**
     * GetItemAction
     * Este método testea el obtener un grupo de trámite dependiendo del que fuera creado por el método PostAction
     * Annotation depends permite generar una dependencia explicita entre métodos,
     * para el caso particular entre PostAction y GetItemAction
     * Endpoint: puntosatencion/{puntoAtencionId}/grupostramites/{grupotramitesId}
     * @depends testPostAction
     */

    public function testGetItemAction($gruposTramitesId)
    {
        $this->gruposTramitesId = $gruposTramitesId;
        $client = static::createClient();
        $client->followRedirects();
        $client->request('GET', '/api/v1.0/puntosatencion/1/grupostramites/' . $this->gruposTramitesId);
        $this->assertEquals(200, $client->getResponse()->getStatusCode());
    }

    /**
     * PutAction
     * Este método testea el modificar un grupo de trámites para lo cual se emplea el creado por el métod PostAction
     * $params Array conteniendo los datos que queremos reemplazar en el grupo de trámites creado
     * con el método PostAction
     * Endpoint: puntosatencion/2/grupostramites/{grupotramitesId}
     * Annotation depends permite generar una dependencia explicita entre métodos,
     * para el caso particular entre PostAction y PutAction
     * @depends testPostAction
     */

    public function testPutAction($grupoTramitesId)
    {
        $this->gruposTramitesId = $grupoTramitesId;
        $client = static::createClient();
        $client->followRedirects();
        $params = [
            'nombre' => 'gp::23:01',
            'horizonte' => 30,
            'tramites' => [
                45
            ],
            'intervalo' => 30
        ];
        $client->request('PUT', '/api/v1.0/puntosatencion/1/grupostramites/' . $this->gruposTramitesId, $params);
        $this->assertEquals(200, $client->getResponse()->getStatusCode());
    }

    /**
     * DeleteAction
     * Método que permite testear el borrado de un grupo de trámites
     * Endpoint: puntosatencion/{puntoAtencionId}/grupostramites/{grupoTramiteId}
     * Annotation depends permite generar una dependencia explicita entre métodos,
     * para el caso particular entre PostAction y DeleteAction
     * @depends testPostAction
     */
    public function testDeleteAction($gruposTramitesId)
    {
        $this->gruposTramitesId = $gruposTramitesId;
        $client = static::createClient();
        $client->followRedirects();
        $client->request('DELETE', '/api/v1.0/puntosatencion/1/grupostramites/' . $this->gruposTramitesId);
        $this->assertEquals(200, $client->getResponse()->getStatusCode());
    }

    /**
     * GetItemActionNotFound
     * Método que testea que si no existe el item retorne el valor correcto
     * Endpoint: puntosatencion/{puntoAtencionId}/grupostramites/{grupoTramiteId}
     * Annotation depends permite generar una dependencia explicita entre métodos,
     * para el caso particular entre PostAction y GetItemActionNotFound
     * @depends testPostAction
     */
    public function testGetItemActionNotFound($gruposTramitesId)
    {
        $this->gruposTramitesId = $gruposTramitesId;
        $client = static::createClient();
        $client->followRedirects();
        $client->request('GET', '/api/v1.0/puntodeatencion/1/grupotgramites/' . $this->gruposTramitesId);
        $this->assertEquals(404, $client->getResponse()->getStatusCode());
    }

    /**
     * PutActionBadRequest
     * Método que testea que ante un badRequest el error se retorne correctamente
     * Endpoint: puntosatencion/{puntoAtencionId}/grupostramites/{grupoTramiteId}
     * params Array conteniendo los datos que queremos reemplazar en el grupo de trámites creado
     * con el mátodo PostAction
     * Annotation depends permite generar una dependencia explicita entre métodos,
     * para el caso particular entre PostAction y PutActionBadRequest
     * @depends testPostAction
     */
    public function testPutActionBadRequest($gruposTramitesId)
    {
        $this->gruposTramitesId = $gruposTramitesId;
        $client = static::createClient();
        $client->followRedirects();
        $params = [
            'nombre' => 'gp::test:03',
            'horizonte' => 30,
            'tramites' => [
                4
            ],
            'intervalo' => 15
        ];
        $client->request('PUT', '/api/v1.0/puntosatencion/1/grupostramites/' . $this->gruposTramitesId, $params);
        $this->assertEquals(400, $client->getResponse()->getStatusCode());
    }
}

<?php

namespace ApiV1Bundle\Tests\Controller;

/**
 * Class AreasControllerTest
 * @package ApiV1Bundle\Tests\Controller
 */
class AreasControllerTest extends ControllerTestCase
{
    /**
     * @var integer $areaId
     */
    protected $areaId;

    /**
     * GetListAction
     * Este método permite testear el listado de áreas del organismo
     * Endpoint: organismos/{organismoId}/areas
     */
    public function testGetListAction()
    {
        $client = static::createClient();
        $client->followRedirects();

        $token = $this->loginTestUser();
        $headers = [
            'HTTP_AUTHORIZATION' => "Bearer {$token}",
            'CONTENT_TYPE' => 'application/json'
        ];

        $client->request('GET', '/api/v1.0/organismos/1/areas', [], [], $headers);

        $this->assertEquals(200, $client->getResponse()->getStatusCode());
    }

    /**
     * testGetListStructureAction
     * Este método testea la estructura de los objetos devueltos por en el get organismos
     * Endpoint: /organismos
     */
    public function testGetListStructureAction()
    {
        $client = static::createClient();
        $client->followRedirects();

        $token = $this->loginTestUser();
        $headers = [
            'HTTP_AUTHORIZATION' => "Bearer {$token}",
            'CONTENT_TYPE' => 'application/json'
        ];

        $client->request('GET', '/api/v1.0/organismos/1/areas', [], [], $headers);

        $result = json_decode($client->getResponse()->getContent())->result[0];

        $this->assertTrue(property_exists($result, 'id'));
        $this->assertTrue(property_exists($result, 'nombre'));
        $this->assertTrue(property_exists($result, 'abreviatura'));
    }

    /**
     * testGetListWihtOrganismoRolAction
     * Este método testea el listado de organismos con Rol Organismo.
     * Se espera 1 sólo resultado, el área al cual pertenece el usuario
     */
    public function testGetListWihtOrganismoRolAction()
    {
        $client = static::createClient();
        $client->followRedirects();

        $token = $this->loginOrganismoUser();
        $headers = [
            'HTTP_AUTHORIZATION' => "Bearer {$token}",
            'CONTENT_TYPE' => 'application/json'
        ];

        $client->request('GET', '/api/v1.0/organismos/1/areas', [], [], $headers);

        $this->assertEquals(200, $client->getResponse()->getStatusCode());
    }

    /**
     * testGetListWihtAreaRolAction
     * Este método testea el listado de organismos con Rol Área.
     * Se espera 1 sólo resultado, el área al cual pertenece el usuario
     */
    public function testGetListWihtAreaRolAction()
    {
        $client = static::createClient();
        $client->followRedirects();

        $token = $this->loginAreaUser();
        $headers = [
            'HTTP_AUTHORIZATION' => "Bearer {$token}",
            'CONTENT_TYPE' => 'application/json'
        ];

        $client->request('GET', '/api/v1.0/organismos/1/areas', [], [], $headers);

        $this->assertEquals(200, $client->getResponse()->getStatusCode());
        $this->assertEquals(1, json_decode($client->getResponse()->getContent())->metadata->resultset->count);
    }

    /**
     * testGetListWihtAreaRolAction
     * Este método testea el listado de áreas con Rol PuntoAtencion.
     * Se espera 1 sólo resultado, el área al cual pertenece el usuario
     */
    public function testGetListWihtPuntoAtencionRolAction()
    {
        $client = static::createClient();
        $client->followRedirects();

        $token = $this->loginPuntoAtencionUser();
        $headers = [
            'HTTP_AUTHORIZATION' => "Bearer {$token}",
            'CONTENT_TYPE' => 'application/json'
        ];

        $client->request('GET', '/api/v1.0/organismos/1/areas', [], [], $headers);

        $this->assertEquals(200, $client->getResponse()->getStatusCode());
        $this->assertEquals(1, json_decode($client->getResponse()->getContent())->metadata->resultset->count);
    }

    /**
     * GetPuntosActencionAction Este método permite testear el listado de puntos de atención del área
     * Endpoint: organismos/{organismoId}/areas/{areaId}/puntoatencion
     */
    public function testGetPuntosAtencionAction()
    {
        $client = static::createClient();
        $client->followRedirects();

        $token = $this->loginPuntoAtencionUser();
        $headers = [
            'HTTP_AUTHORIZATION' => "Bearer {$token}",
            'CONTENT_TYPE' => 'application/json'
        ];

        $client->request('GET', '/api/v1.0/organismos/1/areas/1/puntoatencion', [], [], $headers);
        $this->assertEquals(200, $client->getResponse()->getStatusCode());
    }

    /**
     * GetAreaTramitesAction Este método permite testear el listado de los trámites asociados al área
     * Endpoint: organismos/{organismosId}/areas/{areasId}/tramites
     */
    public function testGetAreaTramitesAction()
    {
        $client = static::createClient();
        $client->followRedirects();
        $client->request('GET', '/api/v1.0/organismos/1/areas/1/tramites');
        $this->assertEquals(200, $client->getResponse()->getStatusCode());
    }

    /**
     * GetAreabreviaturaBuscarAction Este método permite testear el buscador de área por abreviatura
     * Endpoint: organismos/{organismoId}/area/buscar
     */
    public function testGetAreaAbreviaturaBuscarAction()
    {
        $client = static::createClient();
        $client->followRedirects();
        $params = [
            'offset' => '0',
            'limit' => '10',
            'q' => 'df4'
        ];
        $client->request('GET', '/api/v1.0/organismos/1/area/buscar', $params);
        $this->assertEquals(200, $client->getResponse()->getStatusCode());
    }

    /**
     * GetAreabreviaturaBuscarAction Este método permite testear el buscador de área por nombre
     * Endpoint: organismos/{organismoId}/area/buscar
     */
    public function testGetAreaNombreBuscar()
    {
        $client = static::createClient();
        $client->followRedirects();
        $params = [
            'offset' => '0',
            'limit' => '10',
            'q' => 'AFI'
        ];
        $client->request('GET', '/api/v1.0/organismos/1/area/buscar', $params);
        $this->assertEquals(200, $client->getResponse()->getStatusCode());
    }

    /**
     * postAction Este método realiza un alta de un área nueva y retorna el id para poder utilizarlo en otros métodos
     * Endpoint: organismos/{organismoId}/areas
     * @return int
     */
    public function testPostAction()
    {
        $client = static::createClient();
        $client->followRedirects();
        $params = [
            'nombre' => 'Area de prueba',
            'abreviatura' => 'ARELIM'
        ];
        $client->request('POST', '/api/v1.0/organismos/1/areas', $params);
        $areaNuevaDatos = json_decode($client->getResponse()->getContent(), true);
        $this->areaId   = $areaNuevaDatos['additional']['id'];
        $this->assertEquals(200, $client->getResponse()->getStatusCode());
        return $this->areaId;
    }

    /**
     * GetItemAction Este método testea el área creada con el métod postAction
     * Endpoint: organismos/{organismoId}/areas/
     *
     * @param Integer $areaId Este id corresponde a la nueva área creada en el método postAction
     * Annotation depends permite generar una dependencia explicita entre métodos, pera el caso particular entre PostAction y GetItemAction
     *
     * @depends testPostAction
     */
    public function testGetItemAction($areaId)
    {
        $this->areaId = $areaId;
        $client       = static::createClient();
        $client->followRedirects();
        $client->request('GET', '/api/v1.0/organismos/1/areas/' . $this->areaId);
        $this->assertEquals(200, $client->getResponse()->getStatusCode());
    }

    /**
     * PutAction Este método testea la modificación del área creada con el método postAction
     * Endpoint: organismos/{organismoId}/areas/{areaId}
     *
     * @param Integer $areaId Este id corresponde a la nueva área creada en el método postAction
     * Array $params Pasamos los datos que queremos modificar del área creada con el método postAction
     * Annotation depends permite generar una dependencia explicita entre métodos, pera el caso particular entre PostAction y PutAction
     *
     * @depends testPostAction
     */
    public function testPutAction($areaId)
    {
        $this->areaId = $areaId;
        $client       = static::createClient();
        $client->followRedirects();
        $params = [
            'nombre' => 'Area a ser borrada',
            'abreviatura' => 'AMOD'
        ];
        $client->request('PUT', '/api/v1.0/organismos/1/areas/' . $this->areaId, $params);
        $this->assertEquals(200, $client->getResponse()->getStatusCode());
    }

    /**
     * DeleteAction Este método testea el borrado del área creada con el método postAction
     * Endpoint: organismos/{organismoId}/areas/{areaId}
     *
     * @param Integer $areaId Este id corresponde a la nueva área creada en el método postAction
     * Annotation depends permite generar una dependencia explicita entre métodos, pera el caso particular entre PostAction y DeleteAction
     *
     * @depends testPostAction
     */
    public function testDeleteAction($areaId)
    {
        $this->areaId = $areaId;
        $client       = static::createClient();
        $client->followRedirects();
        $client->request('DELETE', '/api/v1.0/organismos/1/areas/' . $this->areaId);
        $this->assertEquals(200, $client->getResponse()->getStatusCode());
    }

    /**
     * SearchAreaTramitesBuscarActionNotFound Este método testea el buscar un trámite y que el mismo no se encuentre porque fue borrado del área creada con el método postAction
     * Endpoint: organismos/{organismoId}/area/{areaId}/tramites/buscar
     * @params array con el nombre del trámite a buscar - mínimo 3 letras
     * Annotation depends permite generar una dependencia explicita entre métodos, pera el caso particular entre PostAction y SearchAreaTramitesBuscarAction
     * @depends testPostAction
     */
    public function testSearchAreaTramitesBuscarActionNotFound()
    {
        $client = static::createClient();
        $client->followRedirects();
        $params = [
            'nombre'=>'mau'
        ];
        $client->request('GET', '/api/v1.0/organismos/1/area/1/tramites/buscar', $params);
        $this->assertEquals(404, $client->getResponse()->getStatusCode());
    }

    /**
     * GetAreaNombreBuscarNotFound Este método testea el buscar un área por nombre y que la misma no se encuentre
     * Endpoint: organismos/{organismoId}/areas/
     * @params array con el nombre del trámite a buscar - mínimo 3 letras
     */
    public function testGetAreaNombreBuscarNotFound()
    {
        $client = static::createClient();
        $params = [
            'nombre' => 'Area de Prueba',
            'abreviatura' => 'ADP'
        ];
        $client->request('POST', '/api/v1.0/organismos/1/areas/', $params);
        $this->assertEquals(404, $client->getResponse()->getStatusCode());
    }



    /**
     * GetItemActionNotFound Este método testea que no exista el área utilizando el Id del área creado con el método PostAction
     * Endpoint: organismos/1/areas/{areaId}
     * param Integer $areaId corresponde al ID del área creado con el método PostAction
     * Annotation depends permite generar una dependencia explicita entre métodos, pera el caso particular entre PostAction y GetItemActionNotFound
     * @depends testPostAction
     */
    public function testGetItemActionNotFound($areaId)
    {
        $this->areaId = $areaId;
        $client = static::createClient();
        $client->followRedirects();
        $client->request('GET', '/api/v1.0/organismos/1/areas/'.$this->areaId);
        $this->assertEquals(404, $client->getResponse()->getStatusCode());
    }

    /**
     * PutActionBadRequest Este método testea que el método PUT retorne un BadRequest
     * Endpoint: organismos/{organismoId}/areas/{areaId}
     * param Integer $areaId corresponde al ID del área creado con el método PostAction
     * $params Array conteniendo los datos a modificar del área
     * Annotation depends permite generar una dependencia explicita entre métodos, pera el caso particular entre PostAction y GetItemActionNotFound
     * @depends testPostAction
     */
    public function testPutActionBadRequest($areaId)
    {
        $this->areaId = $areaId;
        $client = static::createClient();
        $client->followRedirects();
        $params =[
            'nombre'=>'Area modificada',
            'abreviatura'=>'AMOD'
        ];
        $client->request('PUT', '/api/v1.0/organismos/1/areas/'.$this->areaId, $params);
        $this->assertEquals(400, $client->getResponse()->getStatusCode());
    }
}

<?php

namespace ApiV1Bundle\Tests\Controller;

/**
 * Class PuntoAtencionControllerTest
 * @package ApiV1Bundle\Tests\Controller
 */


class PuntoAtencionControllerTest extends ControllerTestCase
{
    /** @var Integer $puntoAtencionId Id de un punto de atención */
    protected $puntoAtencionId;
    //** @var Integer $pADeleteId para borrado */
    protected $pADeleteId;

    /**
     * GetlistAction
     * Este método testea el listado de puntos de atención
     * Recibe dos parámetros para limitar el listado.
     * Endpoint puntosatencion?offset={valor_offset}&limit={valor_limit}
     */
    public function testGetListAction()
    {
        $token = $this->loginTestUser();

        $client = static::createClient();
        $client->followRedirects();

        $headers = [
            'HTTP_AUTHORIZATION' => "Bearer {$token}",
            'CONTENT_TYPE' => 'application/json'
        ];

        $client->request('GET', '/api/v1.0/puntosatencion?offset=20&limit=1', [], [], $headers);
        $this->assertEquals(200, $client->getResponse()->getStatusCode());
    }

    /**
     * testGetListWithOrganismoRolAction
     * Este método testea el listado de puntos de atención
     * Recibe dos parámetros para limitar el listado.
     * Endpoint puntosatencion?offset={valor_offset}&limit={valor_limit}
     */
    public function testGetListWithOrganismoRolAction()
    {
        $token = $this->loginOrganismoUser();

        $client = static::createClient();
        $client->followRedirects();

        $headers = [
            'HTTP_AUTHORIZATION' => "Bearer {$token}",
            'CONTENT_TYPE' => 'application/json'
        ];

        $client->request('GET', '/api/v1.0/puntosatencion?offset=20&limit=1', [], [], $headers);
        $this->assertEquals(200, $client->getResponse()->getStatusCode());
    }

    /**
     * testGetListWithAreaRolAction
     * Este método testea el listado de puntos de atención
     * Recibe dos parámetros para limitar el listado.
     * Endpoint puntosatencion?offset={valor_offset}&limit={valor_limit}
     */
    public function testGetListWithAreaRolAction()
    {
        $token = $this->loginAreaUser();

        $client = static::createClient();
        $client->followRedirects();

        $headers = [
            'HTTP_AUTHORIZATION' => "Bearer {$token}",
            'CONTENT_TYPE' => 'application/json'
        ];

        $client->request('GET', '/api/v1.0/puntosatencion?offset=20&limit=1', [], [], $headers);
        $this->assertEquals(200, $client->getResponse()->getStatusCode());
    }

    /**
     * testGetListWithPuntoAtencionRolAction
     * Este método testea el listado de puntos de atención
     * Recibe dos parámetros para limitar el listado.
     * Endpoint puntosatencion?offset={valor_offset}&limit={valor_limit}
     */
    public function testGetListWithPuntoAtencionRolAction()
    {
        $token = $this->loginPuntoAtencionUser();

        $client = static::createClient();
        $client->followRedirects();

        $headers = [
            'HTTP_AUTHORIZATION' => "Bearer {$token}",
            'CONTENT_TYPE' => 'application/json'
        ];

        $client->request('GET', '/api/v1.0/puntosatencion?offset=20&limit=1', [], [], $headers);
        $this->assertEquals(200, $client->getResponse()->getStatusCode());
        $this->assertEquals(1, json_decode($client->getResponse()->getContent())->metadata->resultset->count);
    }

    /**
     * testSearchPuntoAtencionAction
     * Este método testea la busqueda de un punto de atención
     * $params Array conteniendo el nombre del punto de atención que se quiere buscar
     * Endpoint: puntosatencion/buscar
     */
    public function testSearchPuntoAtencionAction()
    {
        $client = static::createClient();
        $client->followRedirects();
        $params = [
            'q'=>'renaper'
        ];
        $client->request('GET', '/api/v1.0/puntosatencion/buscar', $params);
        $this->assertEquals(200, $client->getResponse()->getStatusCode());
    }

    /**
     * ListTramitesAction
     * Este método testea el listado de trámites de un punto de atención
     * Endpoint: puntosatencion/{puntoAtencionId}/tramites
     */
    public function testListTramitesAction()
    {
        $client = static::createClient();
        $client->followRedirects();
        $client->request('GET', '/api/v1.0/puntosatencion/1/tramites');
        $this->assertEquals(200, $client->getResponse()->getStatusCode());
    }

    /**
     * AddDiaNoHabilAction
     * Este método testea el agregar un día no laborable para un punto de atención
     * $params array conteniendo la fecha que se desea marcar como día no laborable del punto de atención
     * Endpoint: puntosatencion/{puntoAtencionId}/diaNoHabil
     */
    public function testAddDiaNoHabilAction()
    {
        $client = static::createClient();
        $client->followRedirects();
        $params = [
            'fecha'=>'2017-12-08'
        ];
        $client->request('POST', '/api/v1.0/puntosatencion/1/diaNoHabil', $params);
        $this->assertEquals(200, $client->getResponse()->getStatusCode());
    }

    /**
     * EliminarDiaNoHabilAction
     * Este método testea el transformar en laborable un día de un punto de atención que previamente se marcara como día no laborable
     * $params array conteniendo la fecha que se desea marcar como día laborable del punto de atención
     * Endpoint: puntosatencion/{puntoAtencionId}/habilitarFecha
     */
    public function testEliminarDiaNoHabilAction()
    {
        $client = static::createClient();
        $client->followRedirects();
        $params = [
            'fecha'=>'2017-12-08'
        ];
        $client->request('POST', '/api/v1.0/puntosatencion/1/habilitarFecha',$params);
        $this->assertEquals(200, $client->getResponse()->getStatusCode());
    }

    /**
     * GetTramitesDisponiblesAction
     * Este método testea los trámites disponibles de un punto de atención
     * Endpoint: puntosatencion/{puntoAtencionId}/tramitesdisponibles
     */
    public function testGetTramitesDisponiblesAction()
    {
        $client = static::createClient();
        $client->followRedirects();
        $client->request('GET', '/api/v1.0/puntosatencion/1/tramitesdisponibles');
        $this->assertEquals(200, $client->getResponse()->getStatusCode());
    }

    /**
     * PostAction
     * Este método testea el alta de un punto de atención
     * $parameters Array conteniendo los datos necesarios para poder dar de alta un punto de atención nuevo.
     * Endpoint: /puntosatencion
     * @return integer
     */
    public function testPostAction()
    {
        $client = static::createClient();
        $client->followRedirects();
        $parameters = [
            'area'=>'6',
            'nombre'=>'Punto de atencion de prueba',
            'direccion'=>'Calle falsa 123',
            'horarios'=>'Lunes a viernes de 9 a 18',
            'intervalo'=>'0.5',
            'latitud'=>'-34.6033',
            'longitud'=>'-58.381',
            'provincia'=>'2',
            'localidad'=>2,
            'estado'=>'1'
        ];
        $client->request('POST', '/api/v1.0/puntosatencion', $parameters);
        $puntoAtencionDatos = json_decode($client->getResponse()->getContent());
        $this->puntoAtencionId = $puntoAtencionDatos->additional->id;
        $this->assertEquals(200, $client->getResponse()->getStatusCode());
        return $this->puntoAtencionId;
    }

    /**
     * PostForDeleteAction
     * Este método testea el alta de un punto de atención para luego borrarlo en el método DeleteAction
     * @return integer
     */
    public function testPostForDeleteAction()
    {
        $client = static::createClient();
        $client->followRedirects();
        $parameters = [
            'area'=>'6',
            'nombre'=>'Punto de atencion de prueba',
            'direccion'=>'Calle falsa 123',
            'horarios'=>'Lunes a viernes de 9 a 18',
            'intervalo'=>'0.5',
            'latitud'=>'-34.6033',
            'longitud'=>'-58.381',
            'provincia'=>'2',
            'localidad'=>2,
            'estado'=>'1'
        ];
        $client->request('POST', '/api/v1.0/puntosatencion', $parameters);
        $pAtencionDatos = json_decode($client->getResponse()->getContent());
        $this->pADeleteId = $pAtencionDatos->additional->id;
        $this->assertEquals(200, $client->getResponse()->getStatusCode());
        return $this->pADeleteId;
    }

    /**
     * GetItemAction
     * Este método testea el poder obtener un punto de atención determinado. Para el caso se usa el PA que fuera dado de alta con el método PostAction
     * Endpoint; /puntosatencion/{puntoAtencionId}
     * Annotation depends permite generar una dependencia explicita entre métodos, pera el caso particular entre PostAction y GetItemAction
     * @depends testPostAction
     */
    public function testGetItemAction($puntoAtencionId)
    {
        $this->puntoAtencionId = $puntoAtencionId;
        $client = static::createClient();
        $client->followRedirects();
        $client->request('GET', '/api/v1.0/puntosatencion/'.$this->puntoAtencionId);
        $this->assertEquals(200, $client->getResponse()->getStatusCode());
    }

    /**
     * PutAction
     * Este método testea el poder modificar un punto de atención determinado. Para el caso se usa el PA que fuera dado de alta con el método PostAction
     * $params Array conteniendo los datos que se van a modificar del punto de atención que fuera dado de alta por el métod PostAction
     * Endpoint; /puntosatencion/{puntoAtencionId}
     * Annotation depends permite generar una dependencia explicita entre métodos, pera el caso particular entre PostAction y PutAction
     * @depends testPostAction
     */
    public function testPutAction($puntoAtecionId)
    {
        $this->puntoAtencionId = $puntoAtecionId;
        $client = static::createClient();
        $client->followRedirects();
        $parameters = [
            "area"=>'6',
            'nombre'=>'Punto de atencion',
            'direccion'=>'Calle falsa 1234',
            'horarios'=>'Lunes a viernes de 9 a 18',
            'intervalo'=>'0.25',
            'latitud'=>'-34.6033',
            'longitud'=>'-58.381',
            'provincia'=>'2',
            'localidad'=>2,
            'estado'=>'1'
        ];
        $client->request('PUT', '/api/v1.0/puntosatencion/'.$this->puntoAtencionId, $parameters);
        $this->assertEquals(200, $client->getResponse()->getStatusCode());
    }

    /**
     * AddTramiteAction
     * Este método testea el poder agregar trámites a un punto de atención determinado. Para el caso se usa el PA que fuera dado de alta con el método PostAction
     * $params Array conteniendo los tramiteId que se van a agregar al punto de atención
     * Endpoint; /puntosatencion/{puntoAtencionId}/tramites
     * Annotation depends permite generar una dependencia explicita entre métodos, pera el caso particular entre PostAction y AddTramiteAction
     * @depends testPostAction
     */
    public function testAddTramiteAction($puntoAtecionId)
    {
        $this->puntoAtencionId = $puntoAtecionId;
        $client = static::createClient();
        $client->followRedirects();
        $params = [
            'tramites'=>[
                '2',
                '42',
                '37',
                '16',
                '50'
            ]
        ];
        $client->request('POST', '/api/v1.0/puntosatencion/'.$this->puntoAtencionId.'/tramites', $params);
        $this->assertEquals(200, $client->getResponse()->getStatusCode());
    }



    /**
     * AddTramiteAction
     * Este método testea el poder borrar un punto de atención determinado. Para el caso se usa el PA que fuera dado de alta con el método PostForDeleteAction
     * El punto de atención creado en PostForDeleteAction se puede borrar ya que no tiene trámites asociados
     * Endpoint; /puntosatencion/{puntoAtencionId}
     * Annotation depends permite generar una dependencia explicita entre métodos, pera el caso particular entre PostForDeleteAction y DeleteAction
     * @depends testPostForDeleteAction
     */
    public function testDeleteAction($puntoAtencionId)
    {
        $this->pADeleteId = $puntoAtencionId;
        $client = static::createClient();
        $client->followRedirects();
        $client->request('DELETE','/api/v1.0/puntosatencion/'.$this->pADeleteId);
        $this->assertEquals(200, $client->getResponse()->getStatusCode());
    }

    /**
     * getItemActionNotFound
     * Este método testea el intento de buscar un item que no existe. Para el caso se usa el PA que fuera dado de alta con el método PostForDeleteAction
     * Endpoint; /puntosatencion/{puntoAtencionId}
     * Annotation depends permite generar una dependencia explicita entre métodos, pera el caso particular entre PostForDeleteAction y getItemActionNotFound
     * @depends testPostForDeleteAction
     */
    public function testgetItemActionNotFound($puntoAtencionId)
    {
        $this->pADeleteId = $puntoAtencionId;
        $client = static::createClient();
        $client->followRedirects();
        $client->request('GET', '/api/v1.0/puntosatencion/'.$this->pADeleteId);
        $this->assertEquals(404, $client->getResponse()->getStatusCode());
    }

    /**
     * PutActionBadRequest
     * Este método testea el modificar un item que no existe. Para el caso se usa el PA que fuera dado de alta con el método PostForDeleteAction
     * Endpoint; /puntosatencion/{puntoAtencionId}
     * $parameters Array conteniendo los datos que van a usarse para modificar el punto de atención previamente creado con el método PostForDeleteAction
     * Annotation depends permite generar una dependencia explicita entre métodos, pera el caso particular entre PostForDeleteAction y PutActionBadRequest
     * @depends testPostForDeleteAction
     */
    public function testPutActionBadRequest($puntoAtencionId)
    {
        $this->pADeleteId = $puntoAtencionId;
        $client = static::createClient();
        $client->followRedirects();
        $parameters = [
            'area'=>'1',
            'nombre'=>'Punto de atencion',
            'direccion'=>'Calle falsa 1234',
            'horarios'=>'Lunes a viernes de 9 a 18',
            'intervalo'=>'0.25',
            'latitud'=>'-34.6033',
            'longitud'=>'-58.381',
            'provincia'=>'2',
            'localidad'=>2,
            'estado'=>'1'
        ];
        $client->request('PUT', '/api/v1.0/puntosatencion/'.$this->pADeleteId, $parameters);
        $this->assertEquals(400, $client->getResponse()->getStatusCode());
    }

    /**
     * testInhabilitarDia
     * Este método permite inhabilitar un día de un punto de atención
     * Endpoint /puntosatencion/{puntosatencionId}/inhabilitarfecha
     */
    public function testInhabilitarDia()
    {
        $token = $this->loginPuntoAtencionUser();
        $client = static::createClient();
        $client->followRedirects();

        $headers = [
            'HTTP_AUTHORIZATION' => "Bearer $token",
            'CONTENT_TYPE' => 'application/json'
        ];

        $parameters = [
            'fecha' => '2020-12-01'
        ];

        $client->request('POST', '/api/v1.0/puntosatencion/1/inhabilitarfecha', $parameters, [], $headers);
        $this->assertEquals($client->getResponse()->getStatusCode(), 200);
    }

    /**
     * testInhabilitarDia
     * Este método permite testear que falle inhabilitar un día de un punto de atención
     * Endpoint /puntosatencion/{puntosatencionId}/inhabilitarfecha
     */
    public function testInhabilitarDiaFallido()
    {
        $token = $this->loginPuntoAtencionUser();
        $client = static::createClient();
        $client->followRedirects();

        $headers = [
            'HTTP_AUTHORIZATION' => "Bearer $token",
            'CONTENT_TYPE' => 'application/json'
        ];

        $parameters = [
            'fecha' => '2020-12-01'
        ];

        $client->request('POST', '/api/v1.0/puntosatencion/1/inhabilitarfecha', $parameters, [], $headers);
        $this->assertEquals($client->getResponse()->getStatusCode(), 400);
    }

    /**
     * testInhabilitarDia
     * Este método permite testear habilitar un día de un punto de atención que está deshabilitado
     * Endpoint /puntosatencion/{puntosatencionId}/habilitarFecha
     */
    public function testEliminarDiaInhabilitado()
    {
        $token = $this->loginPuntoAtencionUser();
        $client = static::createClient();
        $client->followRedirects();

        $headers = [
            'HTTP_AUTHORIZATION' => "Bearer $token",
            'CONTENT_TYPE' => 'application/json'
        ];

        $parameters = [
            'fecha' => '2020-12-01'
        ];

        $client->request('POST', '/api/v1.0/puntosatencion/1/habilitarFecha', $parameters, [], $headers);
        $this->assertEquals($client->getResponse()->getStatusCode(), 200);
    }
}

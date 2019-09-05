<?php

namespace ApiV1Bundle\Tests\Controller;

use ApiV1Bundle\Mocks\SNCExternalServiceMock;

/**
 * Class TurnoControllerTest
 * @package ApiV1Bundle\Tests\Controller
 */

class TurnoControllerTest extends ControllerTestCase
{
    /**
     * GetAction
     * Testea el listado de turnos completo
     * Endpoint: /turnos
     */
    public function testGetAction()
    {
        $client = static::createClient();
        $client->followRedirects();
        $client->request('GET', '/api/v1.0/turnos');
        $this->assertEquals(200, $client->getResponse()->getStatusCode());
    }

    /**
     * PostAction
     * Testea el dar de alta un nuevo turno
     * $params Array conteniendo los datos necesarios para dar de alta un nuevo turno
     * Endpoint: turnos/
     * @return Integer
     */
    public function testPostAction()
    {
        $client = static::createClient();
        $client->followRedirects();
        $fecha = '2018-04-03';
        $hora = '12:00:00';
        $params = [
            'puntoatencion' => 1,
            'tramite' => 26,
            'fecha' => $fecha,
            'hora' => $hora
        ];
        $client->request('POST', '/api/v1.0/turnos', $params);
        $turnoDatos = json_decode($client->getResponse()->getContent());
        $this->assertEquals(200, $client->getResponse()->getStatusCode());
        return $turnoDatos->additional->id;
    }

    /**
     * ConfirmarAction
     * Testea el método confirmar un turno
     * $params Array conteniendo los datos necesarios para confirmar un turno
     * Endpoint: turnos/{turnoId}/confirmar
     * Annotation depends permite generar una dependencia explicita entre métodos,
     * para el caso particular entre PostAction y ConfirmarAction
     * @depends testPostAction
     */
    public function testConfirmarAction($turnoId)
    {
        $client = static::createClient();
        $client->followRedirects();
        $params = [
            'fecha' => '2018-04-03T13:00:00.000Z',
            'hora' => '13:00',
            'alerta' => '2',
            'campos' => [
                'nombre' => 'Juan',
                'apellido' => 'Perez',
                'cuil' => '27-95108435-8',
                'email' => 'juan.perez@gmail.com'
            ]
        ];
        $client->request('PUT', "/api/v1.0/turnos/{$turnoId}/confirmar", $params);
        $this->assertEquals(200, $client->getResponse()->getStatusCode());
    }

    /**
     * GetItemAction
     * Testea el obtner un item que fuera previamente dado de alta con el método PostAction
     * Endpoint: turnos/{turnoId}
     * Annotation depends permite generar una dependencia explicita entre métodos,
     * para el caso particular entre PostAction y GetItemAction
     * @depends testPostAction
     */
    public function testGetItemAction($turnoId)
    {
        $client = static::createClient();
        $client->followRedirects();
        $client->request('GET', '/api/v1.0/turnos/'.$turnoId);
        $this->assertEquals(200, $client->getResponse()->getStatusCode());
        $turnoDatos = json_decode($client->getResponse()->getContent());
        return $turnoDatos->result->codigo;
    }

    /**
     * CancelarAction
     * Testea el cancelar un turno
     * Endpoint: turnos/{turnoId}/cancelar
     * Annotation depends permite generar una dependencia explicita entre métodos,
     * para el caso particular entre PostAction y CancelarAction
     * @depends testGetItemAction
     */
    public function testCancelarAction($turnoCode)
    {
        $client = static::createClient();
        $client->followRedirects();
        $client->request('PUT', "/api/v1.0/turnos/{$turnoCode}/cancelar");
        $this->assertEquals(200, $client->getResponse()->getStatusCode());
    }

    /**
     * DeleteAction
     * Testea el borrar un turno
     * Endpoint: turnos/{turnoId}
     * Annotation depends permite generar una dependencia explicita entre métodos,
     * para el caso particular entre PostAction y DeleteAction
     * @depends testPostAction
     */
    public function testDeleteAction($turnoId)
    {
        $client = static::createClient();
        $client->followRedirects();
        $client->request('DELETE', '/api/v1.0/turnos/'.$turnoId);
        $this->assertEquals(200, $client->getResponse()->getStatusCode());
    }

    /**
     * UltimoGetTurnoTramiteAction
     * Testea el obtener el último turno de un trámite
     * $params Array conteniendo el tramiteId
     * Endpoint: turnos/ultimoturno
     *
     */
    public function testGetUltimoTurnoTramiteAction()
    {
        $client = static::createClient();
        $client->followRedirects();
        $params = [
            'tramite' => 29
        ];
        $client->request('GET', '/api/v1.0/turnos/ultimoturno', $params);
        $this->assertEquals(200, $client->getResponse()->getStatusCode());
    }

    /**
     * UltimoTurnoPuntoAtencionAction
     * Testea el obtener el ultimo turno de un punto de atención
     * $params Array conteniendo el puntoAtencionId
     * Endpoint: turnos/ultimoturno
     */
    public function testGetUltimoTurnoPuntoAtencionAction()
    {
        $client = static::createClient();
        $client->followRedirects();
        $params = [
            'puntoatencion'=> 23
        ];
        $client->request('GET', '/api/v1.0/turnos/ultimoturno', $params);
        $this->assertEquals(200, $client->getResponse()->getStatusCode());
    }

    /**
     * UltimoTurnoPuntoAtencionTramiteAction
     * Testea el obtener el ultimo turno por punto de atencion y tramite
     * $params Array conteniendo los id de punto de atención y tramite
     * Endpoint: turnos/ultimoturno
     */
    public function testUltimoTurnoPuntoAtencionTramiteAction()
    {
        $client = static::createClient();
        $client->followRedirects();
        $params = [
            'puntoatencion'=> 23,
            'tramite'=> 29
        ];
        $client->request('GET', '/api/v1.0/turnos/ultimoturno', $params);
        $this->assertEquals(200, $client->getResponse()->getStatusCode());
    }

    /**
     * UltimoTurnoGrupoTramiteAction
     * Testea el obtener el ultimo turno de un grupo de trámite
     * $params Array conteniendo el grupoTramiteId para buscar el ultimo turno
     * Endpoint: turnos/ultimoturno
     */
    public function testUltimoTurnoGrupoTramiteAction()
    {
        $client = static::createClient();
        $client->followRedirects();
        $params = [
            'grupotramite' => 38
        ];
        $client->request('GET', '/api/v1.0/turnos/ultimoturno', $params);
        $this->assertEquals(200, $client->getResponse()->getStatusCode());
    }

    /**
     * SearchAction
     * Testea el buscar un turno determinado
     * Endpoint: turnos/buscar?cuil=[cuil]&codigo=[codigo]
     *
     */
    public function testSearchAction()
    {
        $client = static::createClient();
        $client->followRedirects();
        $url = '/api/v1.0/turnos/buscar?cuil=20-46973176-7';
        $url .= '&codigo=c2a107a9-cd88-45f5-b382-c058f8e5e6d5';
        $client->request('GET', $url);
        $this->assertEquals(200, $client->getResponse()->getStatusCode());
    }

    /**
     * SearchActionNotFound
     * Testea el buscar un turno que no existe
     * Endpoint: turnos/turnos/buscar?puntoatencion={puntoAtencionId}&tramite={tramiteId}3&offset={valor}&limit={valor}
     * Annotation depends permite generar una dependencia explicita entre métodos,
     * para el caso particular entre PostAction y SearchActionNotFound
     * @depends testPostAction
     */
    public function testSearchActionNotFound()
    {
        $client = static::createClient();
        $client->followRedirects();
        $client->request('GET', '/api/v1.0/turnos/turnos/buscar?puntoatencion=1&tramite=23&offset=0&limit=10');
        $this->assertEquals(404, $client->getResponse()->getStatusCode());
    }

    /**
     * SearchCodigo
     * Testea buscar por código
     * Endpoint: /integracon/turnos/buscar
     */

    public function testSearchCodigo()
    {
        $client = static::createClient();
        $client->followRedirects();
        $params = [
            'codigo' => 'c2a107a9-cd88-45f5-b382-c058f8e5e6d5',
            'puntoatencionid' => 23
        ];

        $externalService = new SNCExternalServiceMock($this->getContainer());
        $signedParams = $externalService->getTestSignedBody($params, false);
        $client->request('POST', '/api/v1.0/integracion/turnos/buscar', $signedParams);
        $this->assertEquals(200, $client->getResponse()->getStatusCode());
    }

    /**
     * GetForSNC
     * Testea el obtener turnos en SNC por fecha y punto de atención
     * EndPoint: turnos/fecha/
     *
     */
    public function testGetForSNC()
    {
        $now = new \DateTime('now');
        $client = static::createClient();
        $client->followRedirects();
        $params = [
            'puntoatencion' =>  1,
            'fecha' => $now->format('Y-m-d')
        ];

        $externalService = new SNCExternalServiceMock($this->getContainer());
        $signedParams = $externalService->getTestSignedBody($params, false);

        $client->request('POST', '/api/v1.0/integracion/turnos/fecha', $signedParams);
        $this->assertEquals(200, $client->getResponse()->getStatusCode());
    }

    /**
     * GetCiudadanoSinCUIL
     * Testea el obtener datos del ciudadano sin el número de CUIL
     * EndPoint: /turnos/ciudadano
     */

    public function testGetCiudadanoSinCUIL()
    {
        $client = static::createClient();
        $client->followRedirects();
        $params = ['cuil' =>  ''];
        $client->request('GET', '/api/v1.0/turnos/ciudadano', $params);
        $this->assertEquals(400, $client->getResponse()->getStatusCode());
    }

    /**
     * GetForSNCLessFecha
     * Testea que de un error 400 pasandole una array con datos erroneos
     * EndPoint: /turnos/fecha
     */

    public function testGetForSNCLessFecha()
    {
        $client = static::createClient();
        $client->followRedirects();
        $params = [
            'puntoatencion' =>  23,
            'fecha' => '2017-11-03'
        ];

        $externalService = new SNCExternalServiceMock($this->getContainer());
        $signedParams = $externalService->getTestSignedBody($params, false);

        $client->request('POST', '/api/v1.0/integracion/turnos/fecha', $signedParams);
        $this->assertEquals(400, $client->getResponse()->getStatusCode());
    }
    
    /**
     * Testea que el listado tenga los datos esperados
     * Endpoint: /turnos
     */
    public function testDatosExpuestos()
    {
        $client = static::createClient();
        $client->followRedirects();
        $client->request('GET', '/api/v1.0/turnos');
        
        $response = json_decode($client->getResponse()->getContent());

        $turno = current($response->result);

        $this->assertObjectHasAttribute('id', $turno);

        $this->assertObjectHasAttribute('punto_atencion', $turno);
        $this->assertObjectHasAttribute('id', $turno->punto_atencion);
        $this->assertObjectHasAttribute('nombre', $turno->punto_atencion);

        $this->assertObjectHasAttribute('tramite', $turno);
        $this->assertObjectHasAttribute('id', $turno->tramite);
        $this->assertObjectHasAttribute('nombre', $turno->tramite);

        $this->assertObjectHasAttribute('campos', $turno);
        $this->assertObjectHasAttribute('codigo', $turno);
        $this->assertObjectHasAttribute('estado', $turno);
        $this->assertObjectHasAttribute('alerta', $turno);

        $this->assertObjectHasAttribute('fecha', $turno);
        $this->assertObjectHasAttribute('hora', $turno);
    }
}

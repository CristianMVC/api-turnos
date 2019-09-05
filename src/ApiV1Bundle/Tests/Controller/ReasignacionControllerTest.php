<?php

namespace ApiV1Bundle\Tests\Controller;


/**
 * Class TurnoControllerTest
 * @package ApiV1Bundle\Tests\Controller
 */

class ReasignacionControllerTest extends ControllerTestCase
{
    /**
     *
     * Testea el listado de turnos completo
     * Endpoint: /api/v1.0/turnos/23/reasignacion?fecha=2017-09-12
     */
    /*
    public function testTurnosAction()
    {
        $client = static::createClient();
        $client->followRedirects();
        $client->request('GET', '/api/v1.0/turnos/1/reasignacion?fecha=2018-05-21');
        $this->assertEquals(200, $client->getResponse()->getStatusCode());
        // data
        $data = json_decode($client->getResponse()->getContent(), true);
        $this->assertTrue(array_key_exists('result', $data));
        $this->assertEquals(185, $data['result']['total_turnos']);
        $this->assertEquals(1, count($data['result']['grupo_tramites']));
        $this->assertEquals(185, $data['result']['grupo_tramites'][0]['total_turnos']);
    }*/

    /**
     *
     * Testea el listado de fechas completo
     * Endpoint: /api/v1.0/turnos/23/reasignacion/38?fecha=2017-09-12
     */
    /*
    public function testFechasAction()
    {
        $client = static::createClient();
        $client->followRedirects();
        $client->request('GET', '/api/v1.0/turnos/1/reasignacion/1?fecha=2018-05-21');
        $this->assertEquals(200, $client->getResponse()->getStatusCode());
        // data
        $data = json_decode($client->getResponse()->getContent(), true);
        $this->assertTrue(array_key_exists('result', $data));
        $this->assertEquals(185, $data['result']['total_turnos']);
        $this->assertEquals(5, count($data['result']['fechas']));
    }*/
}

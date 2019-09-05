<?php

namespace ApiV1Bundle\Tests\Controller;

/**
 * Class FeriadoNacionalControllerTest
 * @package ApiV1Bundle\Tests\Controller
 */

class FeriadoNacionalControllerTest extends ControllerTestCase
{

    /**
     * GetArrayAction
     * Este método permite testear el listado de feriados nacionales y que el resultado retornado sea una array
     * Endpoint: /feriadoNacional
     */
    public function testGetArrayAction()
    {
        $client = static::createClient();
        $client->followRedirects();
        $client->request('GET', '/api/v1.0/feriadoNacional');
        $feriados = json_decode($client->getResponse()->getContent(), true);
        $this->assertTrue(is_array($feriados));
    }

    /**
     * PostAction
     * Este método testea el agregado de un Feriado Nacional nuevo
     * $params Array conteniendo la fecha que se desea dar de alta como feriado Nacional
     * Endpoint: /feriadoNacional
     */
    public function testPostAction()
    {
        $client = static::createClient();
        $client->followRedirects();
        $params = [
            'fecha'=>'2017-11-20'
        ];
        $client->request('POST', '/api/v1.0/feriadoNacional', $params);
        $this->assertEquals(200, $client->getResponse()->getStatusCode());
    }

    /**
     * GetFeriadosNacionalesAction
     * Este método permite testear el endpoint por el cual se obtienen los feriados nacionales
     * Endpoint: /feriadoNacional
     */
    public function testGetFeriadosNacionalesAction()
    {
        $client = static::createClient();
        $client->followRedirects();
        $client->request('GET', '/api/v1.0/feriadoNacional');
        $this->assertEquals(200, $client->getResponse()->getStatusCode());
    }

    /**
     * GetDiasNoLaborablesAction
     * Este método testea el obtener los días no laborales de un punto de atención
     * Endpoint: puntoAtencion/{puntoAtencionId}/diasnolaborales
     */
    public function testGetDiasNoLaborablesAction()
    {
        $client = static::createClient();
        $client->followRedirects();
        $client->request('GET', '/api/v1.0/puntoAtencion/2/diasnolaborales');
        $feriados = json_decode($client->getResponse()->getContent(), true);
        $this->assertTrue(is_array($feriados));
    }

    /**
     * DeleteAction
     * Este método testea el borrado de un día dado de alta como feriado nacional
     * $params contiene la fecha del feriado nacional que queremos borrar
     * Endpoint: feriadoNacional/
     */
    public function testDeleteAction()
    {
        $client = static::createClient();
        $client->followRedirects();
        $fecha = '2017-11-20';
        $client->request('DELETE', '/api/v1.0/feriadoNacional/'.$fecha);
        $this->assertEquals(200, $client->getResponse()->getStatusCode());
    }
}

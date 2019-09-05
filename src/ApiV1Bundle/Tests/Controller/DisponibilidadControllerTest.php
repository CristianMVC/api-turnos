<?php
namespace ApiV1Bundle\Tests\Controller;
use ApiV1Bundle\Entity\Disponibilidad;
use Symfony\Component\Validator\Constraints\DateTime;

/**
 * Class DisponibilidadControllerTest
 * @package ApiV1Bundle\Tests\Controller
 */

class DisponibilidadControllerTest extends ControllerTestCase
{
    /**
     * GetPuntosAtencionByTramiteProvinciaLocalidadAction
     * Este método testea que se pueda obtener la disponibilidad de un punto de atencion por
     * trámiteId, provinciaId y localidadId
     * Endpoint = disponibilidad/puntosatencion?tramiteId={tramiteId}&provincia={provinciaId}&localidad={localidadId}
     */
    public function testGetPuntosAtencionByTramiteProvinciaLocalidadAction()
    {
        $client = static::createClient();
        $client->followRedirects();
        $client->request('GET', '/api/v1.0/disponibilidad/puntosatencion?tramiteId=16&provincia=1&localidad=31');
        $this->assertEquals(200, $client->getResponse()->getStatusCode());
    }

    /**
     * GetDisponibilidadHorariosTramitesAction
     * Este método testea la disponibilidad por tramiteId y fecha
     * $params array conteniendo el tramiteId y la fecha, la fecha debe pasarse en formato TZ
     * Endpoint: disponibilidad/horarios/{horarioAtencionId}
     */
    public function testGetDisponibilidadHorariosTramitesAction()
    {
        $client = static::createClient();
        $client->followRedirects();
        $fecha = date('Y-m-d', strtotime('now' . ' +1 day'));

        $params = [
            'tramiteId'=> 16,
            'fecha'=> $fecha
        ];

        $client->request('GET', '/api/v1.0/disponibilidad/horarios/10', $params);
        $content = json_decode($client->getResponse()->getContent());

        $this->assertEquals($content->metadata->puntoAtencion, 10);
        $this->assertTrue(array_key_exists('result', $content));
        $this->assertEquals(200, $client->getResponse()->getStatusCode());
    }

    /**
     * GetPuntosAtencionWithoutParametersAction
     * Este método testea obtener el listado de puntos de atención sin parámetros
     * Endpoint: disponibilidad/puntosatencion
     */
    public function testGetPuntosAtencionWithoutParametersAction()
    {
        $client = static::createClient();
        $client->followRedirects();
        $client->request('GET', '/api/v1.0/disponibilidad/puntosatencion');
        $this->assertEquals(400, $client->getResponse()->getStatusCode());
    }

    /**
     * GetPuntosAtencionWithIncorrectParametersAction
     * Este método testea el listado de puntos de atención con parámetros incorrectos para ver
     * que retorne el valor 400
     * Los parámetros se envían junto a la url del endpoint
     * Endpoint: disponibilidad/puntosatencion?tramiteId={tramiteId}&provincia={provinciaId}&localidad=localidadId
     */
    public function testGetPuntosAtencionWithIncorrectParametersAction()
    {
        $client = static::createClient();
        $client->followRedirects();
        $client->request('GET', '/api/v1.0/disponibilidad/puntosatencion?tramiteId=aa&provincia=100&localidad=22');
        $this->assertEquals(400, $client->getResponse()->getStatusCode());
    }

    /**
     * GetPuntosAtencionByTramiteProvinciaLocalidadFechaAction
     * Este método testea el obtener los puntos de atención por tramiteId, ProvinciaId, localidadId, fecha
     * Los parámetros se envían en la misma URL del endpoint
     * Endpoint: disponibilidad/puntosatencion?tramiteId={tramiteId}6&provincia={provinciaIs}&localidad={localidadId}&fecha={fecha}
     */
    public function testGetPuntosAtencionByTramiteProvinciaLocalidadFechaAction()
    {
        $client = static::createClient();
        $client->followRedirects();
        $fecha = date('Y-m-d', strtotime('now' . ' +30 day'));
        $url = '/api/v1.0/disponibilidad/puntosatencion?tramiteId=15&provincia=7&localidad=926&fecha=' . $fecha;
        $client->request('GET', $url);
        $content = json_decode($client->getResponse()->getContent());

        $this->assertTrue(count($content->result) > 0);
        $this->assertEquals(200, $client->getResponse()->getStatusCode());
    }

    /**
     * GetFechaByTramiteProvinciaLocalidadPuntoAtencionAction
     * Este método testea el obtener las fechas por tramiteId, provinciaId, localidadId y puntoAtencionId
     * Los parámetros se envían en la misma URL del endpoint
     * Endpoint: disponibilidad/fechas?tramiteId={tramiteId}&provincia={provinciaId}&localidad={localidadId}&puntoAtencionId={puntoAtención}
     */
    public function testGetFechaByTramiteProvinciaLocalidadPuntoAtencionAction()
    {
        $client = static::createClient();
        $client->followRedirects();
        $url = '/api/v1.0/disponibilidad/fechas?tramiteId=16&provincia=20&localidad=1837&puntoAtencionId=10';
        $client->request('GET', $url);
        $content = json_decode($client->getResponse()->getContent());

        $this->assertTrue(property_exists($content->result[0], 'fecha'));
        $this->assertEquals(200, $client->getResponse()->getStatusCode());
    }

    /**
     * GetCapacidadByPuntoAtencionGrupoTramiteIdRow
     * Este método testea el obtener la capacidad por Grupo de Tramite rowId
     * Endpoint: disponibilidad/puntosatencion/{IdRow}
     */
    public function testGetCapacidadByPuntoAtencionGrupoTramiteIdRow()
    {
        $client = static::createClient();
        $client->followRedirects();
        $client->request('GET', '/api/v1.0/disponibilidad/puntosatencion/1/grupotramite/1');
        $content = json_decode($client->getResponse()->getContent());
        $this->assertGreaterThan(0, $content->result[0]->cantidadTurnos);
        $this->assertTrue(property_exists($content->result[0], 'idRow'));
        $this->assertTrue(property_exists($content->result[0], 'horaInicio'));
        $this->assertTrue(property_exists($content->result[0], 'horaFin'));
        $this->assertTrue(property_exists($content->result[0], 'cantidadTurnos'));
        $this->assertEquals(200, $client->getResponse()->getStatusCode());
    }

    /**
     * TestPostAction
     * Este método testea el alta de disponibilidad
     * Enpoint:/disponibilidad
     */
    public function testPostAction()
    {
        $client = static::createClient();
        $client->followRedirects();
        $params =[
            'puntoAtencion' => 7,
            'grupoTramite' => 11,
            'rangoHorario' => 85,
            'cantidadTurnos' => 20
        ];
        $client->request('POST', '/api/v1.0/disponibilidad', $params);
        $content = json_decode($client->getResponse()->getContent());

        $disponibilidad = $this->entityManager->getRepository(Disponibilidad::class)->find($content->additional->id);
        $this->entityManager->remove($disponibilidad);
        $this->entityManager->flush();
        $this->assertEquals(200, $client->getResponse()->getStatusCode());
    }

    /**
     * testPutAction
     * Este método testea la modificación de datos de disponibilidad
     * Endpoint: /disponibilidad/{idRow}
     */
    public function testPutAction()
    {
        $client = static::createClient();
        $client->followRedirects();
        $params =[
            'puntoAtencion'=> 7,
            'grupoTramite'=> 11,
            'rangoHorario'=> 62,
            'cantidadTurnos'=> 10
        ];
        $client->request('PUT', '/api/v1.0/disponibilidad/9', $params);
        $this->assertEquals(200, $client->getResponse()->getStatusCode());
    }

    /**
     * testDisponibilidadWithTurnoAction
     * Este método testea la disponibilidad luego de sacar un nuevo turno
     *
     * Endpoint: disponibilidad/horarios/1/{puntoAtencionId}
     */
    public function testDisponibilidadWithTurnoAction()
    {
        $client = static::createClient();
        $client->followRedirects();
        $fecha = date('Y-m-d', strtotime('now' . ' +1 day'));

        $params = [
            'tramiteId'=> 26,
            'fecha'=> $fecha
        ];

        $client->request('GET', '/api/v1.0/disponibilidad/horarios/1', $params);
        $content = json_decode($client->getResponse()->getContent());

        if (isset($content->result[0])) {
            $cantidadTurnos = $content->result[0]->cantidadTurnos;

            $paramsTurno = [
                'puntoatencion' => 1,
                'tramite' => 26,
                'fecha' => $content->result[0]->fecha,
                'hora' => $content->result[0]->horario,
                "alerta" => 1
            ];

            $client->request('POST', '/api/v1.0/turnos', $paramsTurno);
            $client->request('GET', '/api/v1.0/disponibilidad/horarios/1', $params);
            $content = json_decode($client->getResponse()->getContent());
            $this->assertEquals($content->result[0]->cantidadTurnos, $cantidadTurnos - 1);
        }
        $this->assertEquals(200, $client->getResponse()->getStatusCode());
    }

    /**
     * testDisponibilidadWithTurnoAction
     * Este método testea la disponibilidad luego de sacar un nuevo turno y cancelarlo. La disponibilidad debe ser la misma.
     *
     * Endpoint: disponibilidad/horarios/1
     */
    public function testDisponibilidadWithTurnoCanceladoAction()
    {
        $client = static::createClient();
        $client->followRedirects();
        $fecha = date('Y-m-d', strtotime('now' . ' +1 day'));

        $params = [
            'tramiteId'=> 26,
            'fecha'=> $fecha
        ];

        $client->request('GET', '/api/v1.0/disponibilidad/horarios/1', $params);
        $content = json_decode($client->getResponse()->getContent());

        if(isset($content->result[0])) {
            $cantidadTurnos = $content->result[0]->cantidadTurnos;

            $paramsTurno = [
                'puntoatencion' => 1,
                'tramite' => 26,
                'fecha' => $content->result[0]->fecha,
                'hora' => $content->result[0]->horario,
                "alerta" => 1
            ];

            $client->request('POST', '/api/v1.0/turnos', $paramsTurno);
            $contentTurno = json_decode($client->getResponse()->getContent());
            $client->request('PUT', "/api/v1.0/turnos/{$contentTurno->additional->codigo}/cancelar");
            $client->request('GET', '/api/v1.0/disponibilidad/horarios/1', $params);
            $content = json_decode($client->getResponse()->getContent());
            $this->assertEquals($content->result[0]->cantidadTurnos, $cantidadTurnos);
        }
        $this->assertEquals(200, $client->getResponse()->getStatusCode());
    }

    /**
     * testHorariosSuperpuestosAction
     * Este método testea la modificación de datos de disponibilidad
     * Endpoint: /disponibilidad/{idRow}
     */
    public function testHorariosSinSuperposicionAction()
    {
        $client = static::createClient();
        $client->followRedirects();
        $paramsHA1 = [
            'horaInicio' => '20:00',
            'horaFin' => '21:00',
            'diasSemana' => [1]
        ];
        $client->request('POST', '/api/v1.0/puntosatencion/4/horarioatencion', $paramsHA1);

        $ha1 = json_decode($client->getResponse()->getContent());
        $idRow1 = $ha1->additional->id;

        $paramsHA2 = [
            'horaInicio' => '22:00',
            'horaFin' => '23:00',
            'diasSemana' => [1]
        ];

        $client->request('POST', '/api/v1.0/puntosatencion/4/horarioatencion', $paramsHA2);

        $ha2 = json_decode($client->getResponse()->getContent());
        $idRow2 = $ha2->additional->id;

        $params =[
            'puntoAtencion'=> 4,
            'grupoTramite'=> 5,
            'cantidadTurnos'=> 10
        ];

        $client->request('PUT', '/api/v1.0/disponibilidad/'.$idRow1, $params);

        $params =[
            'puntoAtencion'=> 4,
            'grupoTramite'=> 5,
            'cantidadTurnos'=> 10
        ];
        $client->request('PUT', '/api/v1.0/disponibilidad/'.$idRow2, $params);

        $responseStatusCode = $client->getResponse()->getStatusCode();

        $client->request('DELETE', '/api/v1.0/puntosatencion/4/horarioatencion/'.$idRow1);
        $client->request('DELETE', '/api/v1.0/puntosatencion/4/horarioatencion/'.$idRow2);

        $this->assertEquals(200, $responseStatusCode);
    }

    /**
     * testHorariosSuperpuestosAction
     * Este método testea la modificación de datos de disponibilidad
     * Endpoint: /disponibilidad/{idRow}
     */
    public function testHorariosSuperpuestosInferiorAction()
    {
        $client = static::createClient();
        $client->followRedirects();
        $paramsHA1 = [
            'horaInicio' => '03:00',
            'horaFin' => '06:00',
            'diasSemana' => [1]
        ];
        $client->request('POST', '/api/v1.0/puntosatencion/4/horarioatencion', $paramsHA1);

        $ha1 = json_decode($client->getResponse()->getContent());
        $idRow1 = $ha1->additional->id;

        $paramsHA2 = [
            'horaInicio' => '02:00',
            'horaFin' => '04:00',
            'diasSemana' => [1]
        ];

        $client->request('POST', '/api/v1.0/puntosatencion/4/horarioatencion', $paramsHA2);

        $ha2 = json_decode($client->getResponse()->getContent());
        $idRow2 = $ha2->additional->id;

        $params =[
            'puntoAtencion'=> 4,
            'grupoTramite'=> 5,
            'cantidadTurnos'=> 10
        ];

        $client->request('PUT', '/api/v1.0/disponibilidad/'.$idRow1, $params);


        $params =[
            'puntoAtencion'=> 4,
            'grupoTramite'=> 5,
            'cantidadTurnos'=> 10
        ];
        $client->request('PUT', '/api/v1.0/disponibilidad/'.$idRow2, $params);

        $responseStatusCode = $client->getResponse()->getStatusCode();

        $client->request('DELETE', '/api/v1.0/puntosatencion/4/horarioatencion/'.$idRow1);
        $client->request('DELETE', '/api/v1.0/puntosatencion/4/horarioatencion/'.$idRow2);

        $this->assertEquals(200, $responseStatusCode);

    }

    /**
     * testHorariosSuperpuestosAction
     * Este método testea la modificación de datos de disponibilidad
     * Endpoint: /disponibilidad/{idRow}
     */
    public function testHorariosSuperpuestosSuperiorAction()
    {
        $client = static::createClient();
        $client->followRedirects();
        $paramsHA1 = [
            'horaInicio' => '03:00',
            'horaFin' => '06:00',
            'diasSemana' => [1]
        ];
        $client->request('POST', '/api/v1.0/puntosatencion/4/horarioatencion', $paramsHA1);

        $ha1 = json_decode($client->getResponse()->getContent());
        $idRow1 = $ha1->additional->id;

        $paramsHA2 = [
            'horaInicio' => '04:00',
            'horaFin' => '07:00',
            'diasSemana' => [1]
        ];

        $client->request('POST', '/api/v1.0/puntosatencion/4/horarioatencion', $paramsHA2);

        $ha2 = json_decode($client->getResponse()->getContent());
        $idRow2 = $ha2->additional->id;

        $params =[
            'puntoAtencion'=> 4,
            'grupoTramite'=> 5,
            'cantidadTurnos'=> 10
        ];

        $client->request('PUT', '/api/v1.0/disponibilidad/'.$idRow1, $params);


        $params =[
            'puntoAtencion'=> 4,
            'grupoTramite'=> 5,
            'cantidadTurnos'=> 10
        ];
        $client->request('PUT', '/api/v1.0/disponibilidad/'.$idRow2, $params);

        $responseStatusCode = $client->getResponse()->getStatusCode();

        $client->request('DELETE', '/api/v1.0/puntosatencion/4/horarioatencion/'.$idRow1);
        $client->request('DELETE', '/api/v1.0/puntosatencion/4/horarioatencion/'.$idRow2);

        $this->assertEquals(200, $responseStatusCode);

    }

    /**
     * testHorariosSuperpuestosAction
     * Este método testea la modificación de datos de disponibilidad
     * Endpoint: /disponibilidad/{idRow}
     */
    public function testHorariosSuperpuestosInternoAction()
    {
        $client = static::createClient();
        $client->followRedirects();
        $paramsHA1 = [
            'horaInicio' => '03:00',
            'horaFin' => '06:00',
            'diasSemana' => [1]
        ];
        $client->request('POST', '/api/v1.0/puntosatencion/4/horarioatencion', $paramsHA1);

        $ha1 = json_decode($client->getResponse()->getContent());
        $idRow1 = $ha1->additional->id;

        $paramsHA2 = [
            'horaInicio' => '04:00',
            'horaFin' => '05:00',
            'diasSemana' => [1]
        ];

        $client->request('POST', '/api/v1.0/puntosatencion/4/horarioatencion', $paramsHA2);

        $ha2 = json_decode($client->getResponse()->getContent());
        $idRow2 = $ha2->additional->id;

        $params =[
            'puntoAtencion'=> 4,
            'grupoTramite'=> 5,
            'cantidadTurnos'=> 10
        ];

        $client->request('PUT', '/api/v1.0/disponibilidad/'.$idRow1, $params);


        $params =[
            'puntoAtencion'=> 4,
            'grupoTramite'=> 5,
            'cantidadTurnos'=> 10
        ];
        $client->request('PUT', '/api/v1.0/disponibilidad/'.$idRow2, $params);

        $responseStatusCode = $client->getResponse()->getStatusCode();

        $client->request('DELETE', '/api/v1.0/puntosatencion/4/horarioatencion/'.$idRow1);
        $client->request('DELETE', '/api/v1.0/puntosatencion/4/horarioatencion/'.$idRow2);

        $this->assertEquals(200, $responseStatusCode);

    }

    /**
     * testHorariosSuperpuestosAction
     * Este método testea la modificación de datos de disponibilidad
     * Endpoint: /disponibilidad/{idRow}
     */
    public function testHorariosSuperpuestosExteriorAction()
    {
        $client = static::createClient();
        $client->followRedirects();
        $paramsHA1 = [
            'horaInicio' => '03:00',
            'horaFin' => '06:00',
            'diasSemana' => [1]
        ];
        $client->request('POST', '/api/v1.0/puntosatencion/4/horarioatencion', $paramsHA1);

        $ha1 = json_decode($client->getResponse()->getContent());
        $idRow1 = $ha1->additional->id;

        $paramsHA2 = [
            'horaInicio' => '02:00',
            'horaFin' => '07:00',
            'diasSemana' => [1]
        ];

        $client->request('POST', '/api/v1.0/puntosatencion/4/horarioatencion', $paramsHA2);

        $ha2 = json_decode($client->getResponse()->getContent());
        $idRow2 = $ha2->additional->id;

        $params =[
            'puntoAtencion'=> 4,
            'grupoTramite'=> 5,
            'cantidadTurnos'=> 10
        ];

        $client->request('PUT', '/api/v1.0/disponibilidad/'.$idRow1, $params);


        $params =[
            'puntoAtencion'=> 4,
            'grupoTramite'=> 5,
            'cantidadTurnos'=> 10
        ];
        $client->request('PUT', '/api/v1.0/disponibilidad/'.$idRow2, $params);

        $responseStatusCode = $client->getResponse()->getStatusCode();
        
        $client->request('DELETE', '/api/v1.0/puntosatencion/4/horarioatencion/'.$idRow1);
        $client->request('DELETE', '/api/v1.0/puntosatencion/4/horarioatencion/'.$idRow2);

        $this->assertEquals(200, $responseStatusCode);

    }
}


<?php

namespace ApiV1Bundle\Tests\Controller;

/**
 * Class HorarioAtencionControllerTest
 * @package ApiV1Bundle\Tests\Controller
 */

class HorarioAtencionControllerTest extends ControllerTestCase
{
    /** @var integer $idRow */
    protected $idRow;

    /**
     * GetIntervalosTiempoAction
     * Metodo que permite obtener el listado de intervalos de tiempos
     * Endpoint; horariosatencion/intervalos
     */
    public function testGetIntervalosSinHorarioAction() {
        $client = static::createClient();
        $client->followRedirects();
        $client->request('GET', '/api/v1.0/horariosatencion/intervalos/7');
        $data = json_decode($client->getResponse()->getContent(), true);
        $this->assertTrue(count(array_diff($data['result'], [15, 30, 60])) == 0);
        $this->assertEquals(200, $client->getResponse()->getStatusCode());
    }

    /**
     * PostAction
     * Método de permite testear el agregar un nuevo horario de atención
     * $params array conteniendo los datos de HoraInicio, HoraFin y los días en que atiende un punto de atención
     * Endpoint: puntosatencion/{puntoAtencionId}/horarioatencion
     * @return integer
     */
   public function testPostAction()
   {
       $client = static::createClient();
       $client->followRedirects();
       $params = [
           'horaInicio' => '20:00',
           'horaFin' => '21:00',
           'diasSemana' => [4]
       ];
       $client->request('POST', '/api/v1.0/puntosatencion/7/horarioatencion', $params);
       $horarioAtencionNuevo = json_decode($client->getResponse()->getContent(), true);
       $this->idRow = $horarioAtencionNuevo['additional']['id'];
       $this->assertEquals(200, $client->getResponse()->getStatusCode());
       return $this->idRow;
       
   }

    /**
     * testGetIntervalosHorasCompletasAction
     * Permite testear obtener intérvalos horas completas
     * Endpoint: horariosatencion/intervalos/{idRow}
     * @depends testPostAction
     */
    public function testGetIntervalosHorasCompletasAction() {
        $client = static::createClient();
        $client->followRedirects();
        $client->request('GET', '/api/v1.0/horariosatencion/intervalos/7');
        $data = json_decode($client->getResponse()->getContent(), true);
        $this->assertTrue(count(array_diff($data['result'], [15, 30, 60])) == 0);
        $this->assertEquals(200, $client->getResponse()->getStatusCode());
    }

    /**
     * testPost30Action
     * Test de creación de horario de atención con diferencia de 30 minutos
     * Endpoint: puntosatencion/{puntoAtencionId}/horarioatencion
     * @return integer Identificador de fila del horario de atención
     * @depends testGetIntervalosHorasCompletasAction
     */
    public function testPost30Action()
    {
        $client = static::createClient();
        $client->followRedirects();
        $params = [
            'horaInicio' => '10:00',
            'horaFin' => '11:30',
            'diasSemana' => [4]
        ];
        $client->request('POST', '/api/v1.0/puntosatencion/7/horarioatencion', $params);
        $horarioAtencionNuevo = json_decode($client->getResponse()->getContent(), true);
        $idRow = $horarioAtencionNuevo['additional']['id'];
        $this->assertEquals(200, $client->getResponse()->getStatusCode());
        return $idRow;
    }

    /**
     * testGetIntervalosExceso30Action
     * Obtener intervalo creado y debe permitir intervalos de 15 y 30 minutos
     * Endpoint: horariosatencion/intervalos/{idRow}
     * @depends testPost30Action
     */
    public function testGetIntervalosExceso30Action($id) {
        $client = static::createClient();
        $client->followRedirects();
        $client->request('GET', '/api/v1.0/horariosatencion/intervalos/7');
        $data = json_decode($client->getResponse()->getContent(), true);
        $this->assertTrue(count(array_diff($data['result'], [15, 30])) == 0);
        $this->assertEquals(200, $client->getResponse()->getStatusCode());
        return $id;
    }

    /**
     * DeleteAction
     * Método que testea el borrado del horario de atención que fuera creado con el método PostAction
     * Endpoint: puntosatencion/{puntoAtencionId}/horarioatencion/{idRow}
     * Annotation depends permite generar una dependencia explicita entre métodos, pera el caso particular entre PostAction y DeleteAction
     * @depends testGetIntervalosExceso30Action
     */
    public function testDeleteHorario30Action($idRow)
    {
        $client = static::createClient();
        $client->followRedirects();
        $client->request('DELETE', '/api/v1.0/puntosatencion/7/horarioatencion/'.$idRow);
        $this->assertEquals(200, $client->getResponse()->getStatusCode());
    }

    /**
     * testPost15Action
     * Test de creación de horario de atención con diferencia de 15 minutos
     * Endpoint: puntosatencion/{puntoAtencionId}/horarioatencion
     * @return integer
     * @depends testGetIntervalosExceso30Action
     */
    public function testPost15Action()
    {
        $client = static::createClient();
        $client->followRedirects();
        $params = [
            'horaInicio' => '20:00',
            'horaFin' => '21:15',
            'diasSemana' => [5]
        ];
        $client->request('POST', '/api/v1.0/puntosatencion/7/horarioatencion', $params);
        $horarioAtencionNuevo = json_decode($client->getResponse()->getContent(), true);
        $idRow = $horarioAtencionNuevo['additional']['id'];
        $this->assertEquals(200, $client->getResponse()->getStatusCode());
        return $idRow;
    }

    /**
     * testGetIntervalosExceso15Action
     * Obtener intervalo creado y debe permitir intervalos de 15 minutos
     * Endpoint: horariosatencion/intervalos/{idRow}
     * @depends testPost15Action
     */
    public function testGetIntervalosExceso15Action($id) {
        $client = static::createClient();
        $client->followRedirects();
        $client->request('GET', '/api/v1.0/horariosatencion/intervalos/7');
        $data = json_decode($client->getResponse()->getContent(), true);
        $this->assertTrue(count(array_diff($data['result'], [15])) == 0);
        $this->assertEquals(200, $client->getResponse()->getStatusCode());
        return $id;
    }

    /**
     * DeleteAction
     * Método que testea el borrado del horario de atención que fuera creado con el método PostAction
     * Endpoint: puntosatencion/{puntoAtencionId}/horarioatencion/{idRow}
     * Annotation depends permite generar una dependencia explicita entre métodos, pera el caso particular entre PostAction y DeleteAction
     * @depends testGetIntervalosExceso15Action
     */
    public function testDeleteHorario15Action($idRow)
    {
        $client = static::createClient();
        $client->followRedirects();
        $client->request('DELETE', '/api/v1.0/puntosatencion/7/horarioatencion/'.$idRow);
        $this->assertEquals(200, $client->getResponse()->getStatusCode());
    }

    /**
     * GetListAction
     * Método que permite testear el listado de horarios de atención de un punto de atención determinado
     * Endpoint: puntosatencion/{puntoAtencionId}/horarioatencion
     * @depends testPostAction
     */
    public function testGetListAction()
    {
        $client = static::createClient();
        $client->followRedirects();
        $client->request('GET', '/api/v1.0/puntosatencion/1/horarioatencion');
        $this->assertEquals(200, $client->getResponse()->getStatusCode());
    }

    /**
     * GetItemAction
     * Método que permite testear el obtener un horario de atención.
     * Endpoint: puntosatencion/{puntoAtencionId}/horarioatencion/{idRow}
     * Annotation depends permite generar una dependencia explicita entre métodos, pera el caso particular entre PostAction y GetItemAction
     * @depends testPostAction
     */
   public function testGetItemAction($idRow)
   {
       $client = static::createClient();
       $client->followRedirects();
       $client->request('GET', '/api/v1.0/puntosatencion/7/horarioatencion/'.$idRow);
       $this->assertEquals(200, $client->getResponse()->getStatusCode());
   }

    /**
     * PutAction
     * Método que testea el modificar el horario de atención que fuera creado con el método PostAction
     * Endpoint: puntosatencion/{puntoAtencionId}/horarioatencion/{idRow}
     * $params array conteniendo los datos de HoraInicio, HoraFin y los días en que atiende un punto de atención
     * Annotation depends permite generar una dependencia explicita entre métodos, pera el caso particular entre PostAction y PutAction
     * @depends testPostAction
     */
   public function testPutAction($idRow)
   {
       $client = static::createClient();
       $client->followRedirects();
       $params = [
           'horaInicio' => '20:00',
           'horaFin' => '21:00',
           'diasSemana' => [1,2]
       ];
       $client->request('PUT', '/api/v1.0/puntosatencion/7/horarioatencion/'.$idRow, $params);
       $this->assertEquals(200, $client->getResponse()->getStatusCode());
   }

    /**
     * GetListadoIntervalosAction
     * Método que testea el obtener el listado de intervalos de horarios de atención
     * Endpoint: horariosatencion/intervalos
     * @depends testPostAction
     */
    public function testGetListadoIntervalosAction()
    {
        $client = static::createClient();
        $client->followRedirects();
        $client->request('GET', '/api/v1.0/horariosatencion/intervalos/7');
        $this->assertEquals(200, $client->getResponse()->getStatusCode());
    }

    /**
     * DeleteAction
     * Método que testea el borrado del horario de atención que fuera creado con el método PostAction
     * Endpoint: puntosatencion/{puntoAtencionId}/horarioatencion/{idRow}
     * Annotation depends permite generar una dependencia explicita entre métodos, pera el caso particular entre PostAction y DeleteAction
     * @depends testPostAction
     */
   public function testDeleteAction($idRow)
   {
       $client = static::createClient();
       $client->followRedirects();
       $client->request('DELETE', '/api/v1.0/puntosatencion/7/horarioatencion/'.$idRow);
       $this->assertEquals(200, $client->getResponse()->getStatusCode());
   }

    /**
     * GetListadoHorariosAction
     * Método que permite testear el obtener el listado de horarios
     * Endpoint: horariosatencion/listarhorarios
     */
    public function testGetListadoHorariosAction()
    {
        $client = static::createClient();
        $client->followRedirects();
        $client->request('GET', '/api/v1.0/horariosatencion/listarhorarios');
        $this->assertEquals(200, $client->getResponse()->getStatusCode());
    }

    /**
     * PutActionBadRequest
     * Método que testea la modificación de los datos de un horario de atención inexistente
     * Endpoint: puntosatencion/{puntoAtencionId}/horarioatencion/{idRow}
     * $params Array conteniendo los datos que deseamos usar para modificar el horario de atención
     */
   public function testPutActionBadRequestAction()
   {
       $client = static::createClient();
       $client->followRedirects();
       $params = [
           'horaInicio'=>'18:00',
           'horaFin'=>'21:00',
           'diasSemana'=>[2]
       ];
       $client->request('PUT', '/api/v1.0/puntosatencion/7/horarioatencion/100', $params);
       $this->assertEquals(400, $client->getResponse()->getStatusCode());
   }

    /**
     * PostAction horario Superpuesto
     * Método de permite testear el agregar un horario de atención superpuesto
     * $params array conteniendo los datos de HoraInicio, HoraFin y los días en que atiende un punto de atención
     * Endpoint: puntosatencion/{puntoAtencionId}/horarioatencion
     */
    public function testPostActionSuperpuesto()
    {
        $client = static::createClient();
        $client->followRedirects();
        $params = [
            'horaInicio' => '18:00',
            'horaFin' => '21:00',
            'diasSemana' => [1, 2]
        ];
        $client->request('POST', '/api/v1.0/puntosatencion/7/horarioatencion', $params);
        $this->assertEquals(200, $client->getResponse()->getStatusCode());
        $params = [
            'horaInicio' => '16:00',
            'horaFin' => '19:00',
            'diasSemana' => [1]
        ];
        $client->request('POST', '/api/v1.0/puntosatencion/7/horarioatencion', $params);
        $this->assertEquals(200, $client->getResponse()->getStatusCode());
    }
}

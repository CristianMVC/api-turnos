<?php
namespace ApiV1Bundle\Tests\Controller;

/**
 * Class OrganismosControllerTest
 * @package ApiV1Bundle\Tests\Controller
 */
class OrganismosControllerTest extends ControllerTestCase
{
    /**
     * @var Integer $organismoId
     */
    protected $organismoId;

    /**
     * GetListAction
     * Este método testea el listado de organismos
     * $params Array conteniendo los valores para limitar la cantidad de items que se muestran en el listado.
     * Los valores por defecto para offset y limit son 0 y 10 respectivamente
     * Endpoint: /organismos
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

        $client->request('GET', '/api/v1.0/organismos', [], [], $headers);

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

        $token = $this->loginOrganismoUser();
        $headers = [
            'HTTP_AUTHORIZATION' => "Bearer {$token}",
            'CONTENT_TYPE' => 'application/json'
        ];

        $client->request('GET', '/api/v1.0/organismos', [], [], $headers);

        $result = json_decode($client->getResponse()->getContent())->result[0];

        $this->assertTrue(property_exists($result, 'id'));
        $this->assertTrue(property_exists($result, 'nombre'));
        $this->assertTrue(property_exists($result, 'abreviatura'));
    }

    /**
     * testGetListWihtAreaRolAction
     * Este método testea el listado de organismos con Rol Organismo.
     * Resultado esperado 400 ya que un Área no puede listar los Organismos
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

        $client->request('GET', '/api/v1.0/organismos', [], [], $headers);

        $this->assertEquals(200, $client->getResponse()->getStatusCode());
        $this->assertEquals(1, json_decode($client->getResponse()->getContent())->metadata->resultset->count);
    }

    /**
     * testGetListWihtAreaRolAction
     * Este método testea el listado de organismos con Rol Área.
     * Se espera 1 sólo resultado, el organismo al cual pertenece el usuario
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

        $client->request('GET', '/api/v1.0/organismos', [], [], $headers);

        $this->assertEquals(200, $client->getResponse()->getStatusCode());
        $this->assertEquals(1, json_decode($client->getResponse()->getContent())->metadata->resultset->count);
    }

    /**
     * testGetListWihtAreaRolAction
     * Este método testea el listado de organismos con Rol PuntoAtencion.
     * Se espera 1 sólo resultado, el organismo al cual pertenece el usuario
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

        $client->request('GET', '/api/v1.0/organismos', [], [], $headers);

        $this->assertEquals(200, $client->getResponse()->getStatusCode());
        $this->assertEquals(1, json_decode($client->getResponse()->getContent())->metadata->resultset->count);
    }


    /**
     * SearchAction
     * Este método testea el buscador de organismos
     * $params Array conteniendo el valor que se está buscando
     * Endpoint: /organismos/buscar
     */
    public function testSearchAction()
    {
        $client = static::createClient();
        $client->followRedirects();
        $params = [
            'q'=>'ANMaC'
        ];
        $client->request('GET', '/api/v1.0/organismos/buscar', $params);
        $this->assertEquals(200, $client->getResponse()->getStatusCode());
    }

    /**
     * PostAction
     * Este método testea el alta de un nuevo organismo
     * $params Array conteniendo los datos necesarios para dar de alta un nuevo organismo
     * Endpoint: /organismos
     * @return integer
     */
    public function testPostAction()
    {
        $client = static::createClient();
        $client->followRedirects();
        $params = [
            'nombre'=>'Prueba1',
            'abreviatura'=>'P1'
        ];
        $client->request('POST', '/api/v1.0/organismos', $params);
        $datosOrganismo = json_decode($client->getResponse()->getContent());
        $this->organismoId = $datosOrganismo->additional->id;
        $this->assertEquals(200, $client->getResponse()->getStatusCode());
        return $this->organismoId;
    }

    /**
     * GetItemAction
     * Este método testea que se pueda obtener el organismo que fuera creado con el método PostAction
     * @param integer $organismoId Id del organismo recientemente creado con el método PostAction
     * Endpoint: organismos/{organismoId}
     * Annotation depends permite generar una dependencia explicita entre métodos, pera el caso particular entre PostAction y GetItemAction
     * @depends testPostAction
     */
    public function testGetItemAction($organismoId)
    {
        $this->organismoId = $organismoId;
        $client = static::createClient();
        $client->followRedirects();
        $client->request('GET', '/api/v1.0/organismos/'.$this->organismoId);
        $this->assertEquals(200, $client->getResponse()->getStatusCode());
    }

    /**
     * PutAction
     * Este método testea que se puedan modificar los datos del organismo credo con el método PostAction
     * @param integer $organismoId Id del organismo recientemente creado con el método PostAction
     * $params array conteniendo los datos para modificar el organismo creado con el método PostAction
     * Endpoint: organismos/{organismoId}
     * Annotation depends permite generar una dependencia explicita entre métodos, pera el caso particular entre PostAction y PutAction
     * @depends testPostAction
     */
    public function testPutAction($organismoId)
    {
        $this->organismoId = $organismoId;
        $client = static::createClient();
        $client->followRedirects();
        $params = [
            'nombre'=>'Lorem Ipsum dolor',
            'abreviatura'=>'lid'
        ];
        $client->request('PUT', '/api/v1.0/organismos/'.$this->organismoId, $params);
        $this->assertEquals(200, $client->getResponse()->getStatusCode());
    }

    /**
     * DeleteItemAction
     * Este método testea que se pueda borrar un organismo credo con el método PostAction
     * @param integer $organismoId Id del organismo recientemente creado con el método PostAction
     * Endpoint: organismos/{organismoId}
     * Annotation depends permite generar una dependencia explicita entre métodos, pera el caso particular entre PostAction y DeleteItemAction
     * @depends testPostAction
     */
    public function testDeleteItemAction($organismoId)
    {
        $this->organismoId = $organismoId;
        $client = static::createClient();
        $client->followRedirects();
        $client->request('DELETE', '/api/v1.0/organismos/'.$this->organismoId);
        $this->assertEquals(200, $client->getResponse()->getStatusCode());
    }

    /**
     * GetItemActionNotFound
     * Este método testea que arroje un error 404 al buscar un organismo inexistente
     * Endpoint: organismos/{organismoId}
     *
     */
    public function testGetItemActionNotFound()
    {
        $client = static::createClient();
        $client->followRedirects();
        $client->request('GET', '/api/v1.0/organismos/26');
        $this->assertEquals(404, $client->getResponse()->getStatusCode());
    }

    /**
     * PutActionBadRequest
     * Este método testea que arroje un error 400 al intentar modificar un organismo inexistente
     * $params Array conteniendo los datos que van a reemplazar a los previamente existentes.
     * Endpoint: organismos/{organismoId}
     */

    public function testPutActionBadRequest()
    {
        $client = static::createClient();
        $client->followRedirects();
        $params = [
            'nombre'=>'Lorem Ipsum dolor',
            'abreviatura'=>'lid'
        ];
        $client->request('PUT', '/api/v1.0/organismos/49', $params);
        $this->assertEquals(400, $client->getResponse()->getStatusCode());
    }

    /**
     * PutActoinOrganismoInexistente
     * Este método testea que arroje un error 400 al intentar modificar un organismo inexistente
     * $params Array conteniendo los datos que van a reemplzar a los previamente existentes.
     * Endpoint:  /organismos/{organismoId}
     * Annotation depends permite generar una dependencia explicita entre métodos, pera el caso particular entre PostAction y DeleteItemAction
     * @depends testPostAction
     */
    public function testPutActionOrganismoInexistente($organismoId)
    {
        $this->organismoId = $organismoId;
        $client = static::createClient();
        $client->followRedirects();
        $params = [
            'nombre' => 'Prueba',
            'abreviatura' => 'PRI'
        ];
        $client->request('PUT', '/api/v1.0/organismos/' . $this->organismoId, $params);
        $this->assertEquals(400, $client->getResponse()->getStatusCode());
    }
}

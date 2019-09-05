<?php

namespace ApiV1Bundle\Tests\Controller;

/**
 * Class TramiteControllerTest
 * @package ApiV1Bundle\Tests\Controller
 */

class TramiteControllerTest extends ControllerTestCase
{
    /**
     * @var integer $tramiteId
     */
    protected $tramiteId;

    /**
     * GetTramitesAction
     * Este método testea el listado trámites
     * Endpoint: /tramites
     */
    public function testGetTramitesAction()
    {
        $client = static::createClient();
        $client->followRedirects();
        $client->request('GET', '/api/v1.0/tramites');
        $this->assertEquals(200, $client->getResponse()->getStatusCode());
    }

    /**
     * GetTramiteFormularioAction
     * Este método testea el formulario de un determinado trámite pasado como parámetro en la URL
     * Endpoint: tramites/{tramiteId}/formulario
     */
    public function testGetTramiteFormularioAction()
    {
        $client = static::createClient();
        $client->followRedirects();
        $client->request('GET', '/api/v1.0/tramites/2/formulario');
        $this->assertEquals(200, $client->getResponse()->getStatusCode());
    }

    /**
     * GetTramiteRequisitosAction
     * Este método testea los requisitos de un trámite determinado
     * Endpoint tramites/{tramiteId}/requisitos
     */
    public function testGetTramiteRequisitosAction()
    {
        $client = static::createClient();
        $client->followRedirects();
        $client->request('GET', '/api/v1.0/tramites/2/requisitos');
        $this->assertEquals(200, $client->getResponse()->getStatusCode());
    }

    /**
     * GetTramiteHorizonteAction
     * Este método testea el horizonte de un determinado trámite
     * Endpoint: tramites/50/horizonte
     */
    public function testGetTramiteHorizonteAction()
    {
        $client = static::createClient();
        $client->followRedirects();
        $client->request('GET', '/api/v1.0/tramites/50/horizonte');
        $this->assertEquals(200, $client->getResponse()->getStatusCode());
    }

    /**
     * GetTramiteFormularioCamposAction
     * Este método testea el listado de campos de un formulario
     * Endpoint: tramites/formulario/campos
     */
    public function testGetTramiteFormularioCamposAction()
    {
        $client = static::createClient();
        $client->followRedirects();
        $client->request('GET', '/api/v1.0/tramites/formulario/campos');
        $this->assertEquals(200, $client->getResponse()->getStatusCode());
    }

    /**
     * postAction
     * Este método testea el alta de un nuevo trámite
     * $params: Array conteniendo los datos necesarios para dar de alta un nuevo trámite
     * Endpoint: /tramites
     * @return integer
     */
    public function testPostAction()
    {
        $client = static::createClient();
        $client->followRedirects();
        $params = [
            'area' => 6,
            'nombre' => 'tramite de prueba',
            'duracion' => 60,
            'requisitos' =>  [
            "Lorem ipsum dolor sit amet, consectetur adipiscing elit.",
            "Aliquam velit lectus, bibendum a arcu eget, fermentum dignissim nibh.",
            "In hac habitasse platea dictumst. Nunc auctor semper mauris, in tempus diam.",
            "Suspendisse ac tempor neque. Nam eget lobortis turpis.",
            "Phasellus facilisis sem libero, ut mollis orci maximus et.",
            "Vivamus suscipit lectus ut lacus viverra, aliquet eleifend metus accumsan.",
            "Suspendisse ut tellus est."
            ],
            'visibilidad' => 1,
            'excepcional' => 1,
            'campos' =>  [
                [
                    'description' => '',
                    'formComponent' => [
                        'typeValue' => 'text'
                    ],
                    'key' => 'nombre',
                    'label' => 'Nombre',
                    'order' => 1,
                    'required' => true,
                    'type' => 'textbox'
                ],
                [
                    'description' => '',
                    'formComponent' => [
                        'typeValue' => 'text'
                    ],
                    'key' => 'apellido',
                    'label' => 'Apellido',
                    'order' => 2,
                    'required' => true,
                    'type' => 'textbox'
                ]
            ],
            'idArgentinaGobAr' => 1
        ];
        $client->request('POST', '/api/v1.0/tramites', $params);
        $datosTramite = json_decode($client->getResponse()->getContent(),true);
        $this->tramiteId = $datosTramite['additional']['id'];
        $this->assertEquals(200, $client->getResponse()->getStatusCode());
        return $this->tramiteId;
    }

    /**
     * postActionBadRequest
     * Este método testea que falle el alta de un nuevo trámite, por parametros incorrectos en la creacion del formulario
     * $params: Array conteniendo los datos necesarios para dar de alta un nuevo trámite
     * Endpoint: /tramites
     * @return integer
     */
    public function testPostActionBadrequest()
    {
        $client = static::createClient();
        $client->followRedirects();
        $params = [
            'area' => 6,
            'nombre' => 'tramite de prueba',
            'duracion' => 60,
            'requisitos' =>  [
                "Lorem ipsum dolor sit amet, consectetur adipiscing elit.",
                "Aliquam velit lectus, bibendum a arcu eget, fermentum dignissim nibh.",
                "In hac habitasse platea dictumst. Nunc auctor semper mauris, in tempus diam.",
                "Suspendisse ac tempor neque. Nam eget lobortis turpis.",
                "Phasellus facilisis sem libero, ut mollis orci maximus et.",
                "Vivamus suscipit lectus ut lacus viverra, aliquet eleifend metus accumsan.",
                "Suspendisse ut tellus est."
            ],
            'visibilidad' => 1,
            'excepcional' => 1,
            'idArgentinaGobAr' => 1,
            'campos' =>  [
                [
                    'description' => '',
                    'formComponent' => [
                        'typeValue' => ''
                    ],
                    'key' => 'nombre',
                    'label' => 'Nombre',
                    'order' => 1,
                    'required' => true,
                    'type' => 'textbox'
                ],
                [
                    'description' => '',
                    'formComponent' => [
                        'typeValue' => 'text'
                    ],
                    'key' => 'apellido',
                    'label' => 'Apellido',
                    'order' => 2,
                    'required' => true,
                    'type' => 'textbox'
                ],
                [
                    'description' => '',
                    'formComponent' => [],
                    'key' => 'tipo-documento',
                    'label' => 'Tipo Documento',
                    'order' => 3,
                    'required' => false,
                    'type' => 'dropdown'
                ]
            ]
        ];
        $client->request('POST', '/api/v1.0/tramites', $params);
        $this->assertEquals(400, $client->getResponse()->getStatusCode());
        $content = json_decode($client->getResponse()->getContent(),true);
        $this->assertTrue(in_array('En el campo nombre se debe especificar el tipo de textbox', $content['userMessage']['errors']));
        $this->assertTrue(in_array('En el campo tipo-documento se debe especificar la lista de opciones', $content['userMessage']['errors']));
    }

    /**
     * GetItemAction Este método testea el obtener un tramite previamente creado con el métod postAction
     * Endpoint: tramites/{tramiteId}
     * Annotation depends permite generar una dependencia explicita entre métodos, pera el caso particular entre PostAction y GetItemAction
     * @depends testPostAction
     */

    public function testGetItemAction($tramiteId)
    {
        $this->tramiteId = $tramiteId;
        $client = static::createClient();
        $client->followRedirects();
        $client->request('GET', '/api/v1.0/tramites/'.$this->tramiteId);
        $this->assertEquals(200, $client->getResponse()->getStatusCode());
    }

    /**
     * PutAction
     * Endpoint tramites/{tramiteId}
     * $params: Array conteniendo los datos necesarios para modificar el trámite creado previamente por el método PostAction
     * Annotation depends permite generar una dependencia explicita entre métodos, pera el caso particular entre PostAction y PutAction
     * @depends testPostAction
     */
    public function testPutAction($tramiteId)
    {
        $this->tramiteId = $tramiteId;
        $client = static::createClient();
        $client->followRedirects();
        $params = [
            'area' => 6,
            'nombre' => 'tramite de prueba',
            'duracion' => 60,
            'requisitos' => [
            "Lorem ipsum dolor sit amet, consectetur adipiscing elit.",
            "Aliquam velit lectus, bibendum a arcu eget, fermentum dignissim nibh.",
            "In hac habitasse platea dictumst. Nunc auctor semper mauris, in tempus diam.",
            "Suspendisse ac tempor neque. Nam eget lobortis turpis.",
            "Phasellus facilisis sem libero, ut mollis orci maximus et.",
            "Vivamus suscipit lectus ut lacus viverra, aliquet eleifend metus accumsan.",
            "Suspendisse ut tellus est."
            ],
            'visibilidad' => 0,
            'excepcional' => 0,
            'campos' => [
                [
                    'description' => '',
                    'formComponent' => [
                        'typeValue' => 'text'
                    ],
                    'key' => 'nombre',
                    'label' => 'Nombre',
                    'order' => 1,
                    'required' => true,
                    'type' => 'textbox'
                ]
            ]
        ];
        $client->request('PUT', '/api/v1.0/tramites/'.$this->tramiteId, $params);
        $this->assertEquals(200, $client->getResponse()->getStatusCode());
    }

    /**
     * PutActionBadRequest
     *  Este método testea que falle la modificación de un trámite, por parametros incorrectos en la creacion del formulario
     * Endpoint tramites/{tramiteId}
     * $params: Array conteniendo los datos necesarios para modificar el trámite creado previamente por el método PostAction
     * Annotation depends permite generar una dependencia explicita entre métodos, pera el caso particular entre PostAction y PutAction
     * @depends testPostAction
     */
    public function PutActionBadRequest($tramiteId)
    {
        $this->tramiteId = $tramiteId;
        $client = static::createClient();
        $client->followRedirects();
        $params = [
            'area' => 6,
            'nombre' => 'tramite de prueba',
            'duracion' => 60,
            'requisitos' =>  [
                "Lorem ipsum dolor sit amet, consectetur adipiscing elit.",
                "Aliquam velit lectus, bibendum a arcu eget, fermentum dignissim nibh.",
                "In hac habitasse platea dictumst. Nunc auctor semper mauris, in tempus diam.",
                "Suspendisse ac tempor neque. Nam eget lobortis turpis.",
                "Phasellus facilisis sem libero, ut mollis orci maximus et.",
                "Vivamus suscipit lectus ut lacus viverra, aliquet eleifend metus accumsan.",
                "Suspendisse ut tellus est."
            ],
            'visibilidad' => 1,
            'excepcional' => 0,
            'campos' => [
                [
                    'description' => '',
                    'formComponent' => [
                        'typeValue' => ''
                    ],
                    'key' => 'Apellido',
                    'label' => 'Apellido',
                    'order' => 1,
                    'required' => true,
                    'type' => 'textbox'
                ]
            ]
        ];
        $client->request('PUT', '/api/v1.0/tramites/'.$this->tramiteId, $params);
        $this->assertEquals(400, $client->getResponse()->getStatusCode());
        $content = json_decode($client->getResponse()->getContent(),true);
        $this->assertEquals('Se debe especificar el tipo de textbox', $content['userMessage']['errors']['Apellido']);
    }

    /**
     * DeleteAction
     * Este método testea el borrado de un trámite creado previamente con el método PostAction
     * Endpoint: tramites/{tramiteId}
     * Annotation depends permite generar una dependencia explicita entre métodos, pera el caso particular entre PostAction y DeleteAction
     * @depends testPostAction
     */
    public function testDeleteAction($tramiteId)
    {
        $this->tramiteId = $tramiteId;
        $client = static::createClient();
        $client->followRedirects();
        $client->request('DELETE', '/api/v1.0/tramites/'.$this->tramiteId);
        $this->assertEquals(200, $client->getResponse()->getStatusCode());
    }

    /**
     * GetActionNotFound
     * Este método testea el fallo al intentar recuperar un Item inexistente
     * Endpoint: tramites/{tramiteId}
     * Annotation depends permite generar una dependencia explicita entre métodos, pera el caso particular entre PostAction y GetActionNotFound
     * @depends testPostAction
     */
    public function testGetActionNotFound($tramiteId)
    {
        $this->tramiteId = $tramiteId;
        $client = static::createClient();
        $client->followRedirects();
        $client->request('GET', '/api/v1.0/tramites/'.$this->tramiteId);
        $this->assertEquals(404, $client->getResponse()->getStatusCode());
    }

    /**
     * PutActionBadRequest
     * Este mpetodo testea que falle en intento de modificar un item inexistente
     * Endpoint: tramites/{tramiteId}
     * $params: Array conteniendo los datos necesarios para modificar el trámite creado previamente por el método PostAction
     * Annotation depends permite generar una dependencia explicita entre métodos, pera el caso particular entre PostAction y PutActionBadRequest
     * @depends testPostAction
     */
    public function testPutActionBadRequest($tramiteId)
    {
        $this->tramiteId = $tramiteId;
        $client          = static::createClient();
        $client->followRedirects();
        $params = [
            'area' => 51,
            'nombre' => 'tramite de prueba',
            'duracion'  => 60,
            'requisitos' => [
                "Lorem ipsum dolor sit amet, consectetur adipiscing elit.",
                "Aliquam velit lectus, bibendum a arcu eget, fermentum dignissim nibh.",
                "In hac habitasse platea dictumst. Nunc auctor semper mauris, in tempus diam.",
                "Suspendisse ac tempor neque. Nam eget lobortis turpis.",
                "Phasellus facilisis sem libero, ut mollis orci maximus et.",
                "Vivamus suscipit lectus ut lacus viverra, aliquet eleifend metus accumsan.",
                "Suspendisse ut tellus est."
            ],
            'visibilidad' => 1,
            'excepcional' => 0,
            'campos' => '{"lorem": "ipsum", "dolor": "sit amet"}'
        ];
        $client->request('PUT', '/api/v1.0/tramites/' . $this->tramiteId, $params);
        $this->assertEquals(400, $client->getResponse()->getStatusCode());
    }
    /**
     * GetProvincias
     * Método que testea el obtener las provincias por tramiteId
     * Provincias: Capital Federal, Catamarca, La Rioja, Santa Fe
     * EndPoint: tramites/{tramiteId}/provincias
     */
    public function testGetProvincias()
    {
        $client = static::createClient();
        $client->followRedirects();
        $client->request('GET', '/api/v1.0/tramites/10/provincias');
        $this->assertEquals(200, $client->getResponse()->getStatusCode());
        $content = json_decode($client->getResponse()->getContent(), true);
        $this->assertEquals('Capital Federal', $content['result'][0]['nombre']);
        $this->assertEquals('Catamarca', $content['result'][1]['nombre']);
        $this->assertEquals('La Rioja', $content['result'][2]['nombre']);
        $this->assertEquals('Santa Fe', $content['result'][3]['nombre']);
    }

    /**
     * GetLocalidades
     * Método que testea el obtener las localidades por tramiteId
     * Localidades: San Carlos Centro
     * Endpoint: tramites/{tramiteId}/provincias/{provi}
     */
    public function testGetLocalidades()
    {
        $client = static::createClient();
        $client->followRedirects();
        $client->request('GET', '/api/v1.0/tramites/10/provincias/21');
        $this->assertEquals(200, $client->getResponse()->getStatusCode());
        $content = json_decode($client->getResponse()->getContent(), true);
        $this->assertEquals('San Carlos Centro', $content['result'][0]['nombre']);
    }
}

<?php
/**
 * Created by PhpStorm.
 * User: jtibi
 * Date: 22/11/2017
 * Time: 2:33 PM
 */
namespace ApiV1Bundle\ExternalServices;

use ApiV1Bundle\Entity\PuntoAtencion;
use Symfony\Component\DependencyInjection\Container;
use ApiV1Bundle\ExternalServices\SNCExternalService;
use ApiV1Bundle\Mocks\SNCExternalServiceMock;

/**
 * Class PuntoAtencionIntegration
 * @package ApiV1Bundle\ExternalServices
 */
class PuntoAtencionIntegration extends Integration
{
   /** @var SNCExternalService | SNCExternalServiceMock  */
    private $integrationService;

    /**
     * PuntoAtencionIntegration constructor.
     * @param Container $container
     * @param SNCExternalService $integrationService
     * @param SNCExternalServiceMock $integrationMock
     */
    public function __construct(
        Container $container,
        SNCExternalService $integrationService,
        SNCExternalServiceMock $integrationMock
    ) {
        parent::__construct($container);
        $this->integrationService = $integrationService;
        if ($this->getEnvironment() == 'test') {
            $this->integrationService = $integrationMock;
        }
    }

    /**
     * Agregar un punto de atencion en SNC
     *
     * @param object $puntoAtencion objeto PuntoAtencion
     * @return mixed
     */
    public function agregarPuntoAtencion($puntoAtencion)
    {
        $body = [
            'punto_atencion_id_SNT' => $puntoAtencion->getId(),
            'nombre' => $puntoAtencion->getNombre(),
            'organismo' => $puntoAtencion->getArea()->getOrganismo()->getId(),
            'area' => $puntoAtencion->getArea()->getId()
        ];

        return $this->agregarPuntoAtencionSNC($body);
    }

    /**
     * Agregar un punto de atencion en SNC
     *
     * @param array $body cuerpo de la llamada al servicio externo
     * @return mixed
     */
    private function agregarPuntoAtencionSNC($body)
    {
        $url = $this->integrationService->getUrl('puntosatencion');
        return  $this->integrationService->post($url, $body);
    }

    /**
     * Modificar un punto de atencion en SNC
     *
     * @param integer $id identificador único del punto de atención
     * @param array $params arreglo con los datos del punto de atención
     * @return mixed
     */
    public function editarPuntoAtencion($id, $params)
    {
        $body = [
            'nombre' => $params['nombre']
        ];

        $url = $this->integrationService->getUrl('puntosatencion', $id);
        return $this->integrationService->put($url, $body);
    }

    /**
     * Eliminar un punto de atencion en SNC
     *
     * @param integer $id identificador único del punto de atención
     * @return mixed
     */
    public function eliminarPuntoAtencion($id)
    {
        $url = $this->integrationService->getUrl('puntosatencion', $id);
        return $this->integrationService->delete($url, null);
    }
}

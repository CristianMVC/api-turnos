<?php
/**
 * GrupoTramiteIntegration class
 *
 * @author Fausto Carrera <fcarrera@hexacta.com>
 */
namespace ApiV1Bundle\ExternalServices;

use Symfony\Component\DependencyInjection\Container;
use ApiV1Bundle\ExternalServices\SNCExternalService;
use ApiV1Bundle\Mocks\SNCExternalServiceMock;

/**
 * Class GrupoTramitesIntegration
 * @package ApiV1Bundle\ExternalServices
 */
class GrupoTramitesIntegration extends Integration
{
    /** @var SNCExternalServiceMock |  SNCExternalService */
    private $integrationService;

    /**
     * GrupoTramitesIntegration constructor.
     * @param Container $container
     * @param \ApiV1Bundle\ExternalServices\SNCExternalService $integrationService
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
     * Agregar una cola al Sistema Nacional de Colas
     *
     * @param object $grupoTramite objeto GrupoTramite
     * @param integer $puntoAtencionId identificador único de punto de atención
     * @return mixed
     */
    public function agregarCola($grupoTramite, $puntoAtencionId)
    {
        $body = [
            'nombre' => $grupoTramite->getNombre(),
            'grupoTramite' => (int) $grupoTramite->getId(),
            'puntoAtencion' => (int) $puntoAtencionId
        ];
        $url = $this->integrationService->getUrl('colas');
        return $this->integrationService->post($url, $body);
    }

    /**
     * Modifica una cola del sistema nacional de colas
     *
     * @param integer $id identificador único de cola
     * @param string $nombre Nombre de la cola
     * @return mixed
     */
    public function editarCola($id, $nombre)
    {
        $body = [
            'nombre' => $nombre
        ];
        $url = $this->integrationService->getUrl('colas', $id);
        return $this->integrationService->put($url, $body);
    }

    /**
     * Eliminar una cola del sistema nacional de colas
     *
     * @param integer $id identificador único de cola
     * @return mixed
     */
    public function eliminarCola($id)
    {
        $url = $this->integrationService->getUrl('colas', $id);
        return $this->integrationService->delete($url, null);
    }
}

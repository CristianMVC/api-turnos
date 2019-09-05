<?php
/**
 * Turno Integration class
 *
 * @author Fausto Carrera <fcarrera@hexacta.com>
 */
namespace TotemV1Bundle\ExternalServices;

use Symfony\Component\DependencyInjection\Container;
use ApiV1Bundle\ExternalServices\Integration;
use ApiV1Bundle\ExternalServices\SNCExternalService;
use ApiV1Bundle\Mocks\SNCExternalServiceMock;

/**
 * Class TurnoTotemIntegration
 * @package TotemV1Bundle\ExternalServices
 */
class TurnoTotemIntegration extends Integration
{
    /** @var SNCExternalService | SNCExternalServiceMock  */
    private $integrationService;

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
     * Integración de recepción de turnos
     *
     * @param array $body
     * @return mixed
     */
    public function recepcionarTurno($body)
    {
        $url = $this->integrationService->getUrl('turnos');
        return $this->integrationService->post($url, $body);
    }

    /**
     * Integración de personas delante
     *
     * @param array $body
     * @return mixed
     */
    public function getCantidadDelante($body)
    {
        $url = $this->integrationService->getUrl('turnos.delante');
        return  $this->integrationService->post($url, $body);
    }
}

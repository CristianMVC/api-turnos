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
use ApiV1Bundle\Entity\Validator\CommunicationValidator;

/**
 * Class SecurityIntegration
 * @package ApiV1Bundle\ExternalServices
 */
class SecurityIntegration extends Integration
{
    /** @var SNCExternalService | SNCExternalServiceMock  */
    private $integrationService;
    /** @var CommunicationValidator  */
    private $communicationValidator;

    /**
     * SecurityIntegration constructor.
     * @param Container $container
     * @param SNCExternalService $integrationService
     * @param SNCExternalServiceMock $integrationMock
     * @param CommunicationValidator $communicationValidator
     */
    public function __construct(
        Container $container,
        SNCExternalService $integrationService,
        SNCExternalServiceMock $integrationMock,
        CommunicationValidator $communicationValidator
    ) {
        parent::__construct($container);
        $this->integrationService = $integrationService;
        if ($this->getEnvironment() == 'test') {
            $this->integrationService = $integrationMock;
        }
        $this->communicationValidator = $communicationValidator;
    }

    /**
     * Validamos la comunicación POST
     *
     * @param array $params
     * @return mixed
     */
    public function securePostCommunications($params)
    {
        $url = $this->integrationService->getUrl('test');
        return $this->integrationService->post($url, $params);
    }
}

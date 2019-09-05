<?php
namespace TotemV1Bundle\ApplicationServices;

use Symfony\Component\DependencyInjection\Container;
use ApiV1Bundle\Repository\TurnoRepository;
use TotemV1Bundle\Entity\Validator\TurnoTotemValidator;
use TotemV1Bundle\ExternalServices\TurnoTotemIntegration;
use Ramsey\Uuid\Uuid;
use ApiV1Bundle\Entity\ValidateResultado;
use TotemV1Bundle\Helper\ServicesHelper;

/**
 * Class TramiteServices
 * @package TotemV1Bundle\ApplicationServices
 */
class TurnoServices extends TotemServices
{
    /** @var \ApiV1Bundle\Repository\TurnoRepository  */
    private $turnoRepository;

    /** @var \ApiV1Bundle\Entity\Validator\SNTValidator  */
    private $validator;

    /** @var \TotemV1Bundle\ExternalServices\TurnoTotemIntegration  */
    private $integration;

    /**
     * TramiteServices constructor.
     *
     * @param Container $container
     * @param $tramiteRepository
     */
    public function __construct(
        Container $container,
        TurnoRepository $turnoRepository,
        TurnoTotemValidator $validator,
        TurnoTotemIntegration $integration
    ) {
        parent::__construct($container);
        $this->turnoRepository = $turnoRepository;
        $this->validator = $validator;
        $this->integration = $integration;
    }

    /**
     * Recepcionar turno
     *
     * @param array  $params arreglo con los datos del turno
     * @param callback $sucess Callback para devolver respuesta exitosa
     * @param callback $error Callback para devolver respuesta fallida
     * @return mixed
     */
    public function recepcionar($params, $onSuccess, $onError)
    {
        $result = [];
        // update cuil
        if (! isset($params['excepcional']) || $params['excepcional'] == 0) {
            $params['cuil'] = ServicesHelper::cleanCUIL($params['cuil']);
        }
        // validate params
        $validateResult = $this->validator->validarTurno($params);
        if (! $validateResult->hasError()) {
            try {
                // update the body
                $params['codigo'] = Uuid::uuid4();
                $params['campos'] = [
                    'nombre' => $params['nombre'],
                    'apellido' => $params['apellido'],
                    'cuil' => $params['cuil'],
                ];
                // send the data to SNC
                $validateResult = $this->integration->recepcionarTurno($params);
                if (!$validateResult->hasError()) {
                    $response = $validateResult->getEntity();
                    $result = $response['additional'];
                }
            } catch (Exception $exception) {
                $validateResult = new ValidateResultado(null, [$exception->getMessage()]);
            }
        }

        return $this->processResult(
            $validateResult,
            function () use ($onSuccess, $result) {
                return call_user_func($onSuccess, $result);
            },
            $onError
        );
    }
}

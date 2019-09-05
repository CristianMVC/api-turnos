<?php
namespace ApiV1Bundle\Entity\Factory;

use ApiV1Bundle\Entity\Turno;
use ApiV1Bundle\Entity\ValidateResultado;
use ApiV1Bundle\Repository\PuntoAtencionRepository;
use ApiV1Bundle\Repository\TramiteRepository;
use Ramsey\Uuid\Uuid;
use ApiV1Bundle\Entity\Validator\TurnoValidator;
use Symfony\Component\Validator\Constraints\DateTime;
use Symfony\Component\Validator\Constraints\Time;

/**
 * Class TurnoFactory
 * @package ApiV1Bundle\Entity\Factory
 */

class TurnoFactory
{
    /** @var PuntoAtencionRepository  */
    private $puntoAtencionRepository;
    /** @var TramiteRepository  */
    private $tramiteRepository;
    /** @var TurnoValidator  */
    private $turnoValidator;

    /**
     * TurnoFactory constructor.
     * @param PuntoAtencionRepository $puntoAtencionRepository
     * @param TramiteRepository $tramiteRepository
     * @param TurnoValidator $turnoValidator
     */
    public function __construct(
        PuntoAtencionRepository $puntoAtencionRepository,
        TramiteRepository $tramiteRepository,
        TurnoValidator $turnoValidator
    ) {
        $this->puntoAtencionRepository = $puntoAtencionRepository;
        $this->tramiteRepository = $tramiteRepository;
        $this->turnoValidator = $turnoValidator;
    }

    /**
     * Crea un turno
     *
     * @param array $params array con datos del punto de atención y datos del trámite
     * @return mixed
     */
    public function create($params, $user)
    {
        $validateResultado = $this->turnoValidator->validarCreate($params);
        if (! $validateResultado->hasError()) {
            $fecha = new \DateTime($params['fecha']);
            $hora = new \DateTime($params['hora']);

            $tramite = $this->tramiteRepository->find($params['tramite']);
            
            $origen = isset($params['origen'])?$params['origen']:null;
            $turno = new Turno($validateResultado->getEntity(), $tramite, $fecha, $hora, $user, $origen);
            
            if (isset($params['alerta'])) {
                $turno->setAlerta($params['alerta']);
            }
            return new ValidateResultado($turno, []);

        }
        $errors = $validateResultado->getErrors();
        return new ValidateResultado(null, $errors['errors']);
    }
    
    /**
     * Crea varios turno
     *
     * @param array $params array con datos del punto de atención y datos del trámite
     * @return mixed
     */
    public function createMultiple($params, $user)
    {
        $validateResultadoMultiple = $this->turnoValidator->validarCreateMultiple($params);
        if (! $validateResultadoMultiple->hasError()) {
            foreach ($params["horas"] as $hora) {
                $params["hora"] = $hora;
                $params['origen'] = Turno::ORIGEN_MIARGENTINA;
                $validateResultado = $this->create($params, $user);
                if ($validateResultado->hasError()) {
                    $errors = $validateResultado->getErrors();
                    return new ValidateResultado(null, $errors['errors']);
                }
                $turnos[]=$validateResultado;
            }
            return new ValidateResultado($turnos, []);
        }
       
        $errors = $validateResultadoMultiple->getErrors();
        return new ValidateResultado(null, $errors['errors']);
    }

}

<?php
namespace ApiV1Bundle\Entity\Validator;

use ApiV1Bundle\Entity\ValidateResultado;
use ApiV1Bundle\Repository\DiaNoLaborableTramiteRepository;
use ApiV1Bundle\Repository\TurnoRepository;


/**
 * Class UserValidator
 * @package ApiV1Bundle\Entity\Validator
 */
class DiaNoLaborableTramiteValidator extends SNTValidator
{
    
    /** @var DiaNoLaborableTramiteRepository  */
    private $diaNoLaborableTramiteRepository;
    /** @var TurnoRepository  */
    private $turnoRepository;

    /**
     * GrupoTramitesValidator constructor.
     * @param DiaNoLaborableTramiteRepository $diaNoLaborableTramiteRepository
     * @param TurnoRepository $turnoRepository
     */
    public function __construct(
        DiaNoLaborableTramiteRepository $diaNoLaborableTramiteRepository,
        TurnoRepository $turnoRepository
    ) {
        $this->diaNoLaborableTramiteRepository = $diaNoLaborableTramiteRepository;
        $this->turnoRepository = $turnoRepository;
    }

    /**
     * Validar 
     *
     * @param array $params array con datos a validar
     * @param object $user objeto 
     * @return ValidateResultado
     */
    public function validarDiaNoLaborable($params,$puntoAtencion, $tramite)
    {
        
        $errors = $this->validar($params, [
            'fecha' => 'required:date'
        ]);
        $fecha = new \DateTime($params['fecha']);
        $diaNoLaborable = $this->diaNoLaborableTramiteRepository->findOneBy([
            'puntoAtencion' => $puntoAtencion,
            'tramite' => $tramite,
            'fecha' => $fecha
        ]);

            if ($diaNoLaborable) {
                $errors[] = 'Ya existe un día no laborable con esa fecha';
                return new ValidateResultado(null, $errors);
            }

            $turno = $this->turnoRepository->findOneBy(['puntoAtencion' => $puntoAtencion, 'fecha' => $fecha]);

            if ($turno) {
                $errors[] = 'La fecha seleccionada posee turnos asignados';
                return new ValidateResultado(null, $errors);
            }
        return new ValidateResultado(null, $errors);
    }
   
}

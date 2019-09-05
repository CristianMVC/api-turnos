<?php
namespace ApiV1Bundle\Entity\Validator;

use ApiV1Bundle\Entity\ValidateResultado;
use ApiV1Bundle\Repository\FeriadoNacionalRepository;
use ApiV1Bundle\Repository\TurnoRepository;

/**
 * Class FeriadoNacionalValidator
 * @package ApiV1Bundle\Entity\Validator
 */
class FeriadoNacionalValidator extends SNTValidator
{
    /** @var FeriadoNacionalRepository  */
    private $feriadoNacionalRepository;
    /** @var TurnoRepository  */
    private $turnoRepository;

    /**
     * FeriadoNacionalValidator constructor.
     * @param FeriadoNacionalRepository $feriadoNacionalRepository
     * @param TurnoRepository $turnoRepository
     */
    public function __construct(FeriadoNacionalRepository $feriadoNacionalRepository, TurnoRepository $turnoRepository)
    {
        $this->feriadoNacionalRepository = $feriadoNacionalRepository;
        $this->turnoRepository = $turnoRepository;
    }

    /**
     * Validar crear
     *
     * @param array $params array con datos a validar
     * @return ValidateResultado
     */
    public function validarCreate($params)
    {
        $errors = $this->validar($params, [
            'fecha' => 'required:date'
        ]);

        if (! count($errors)) {
            $fecha = new \DateTime($params['fecha']);
            $feriado = $this->feriadoNacionalRepository->findOneBy(['fecha' => $fecha]);
            if ($feriado) {
                $errors[] = 'Ya existe una Feriado Nacional con esa fecha';
            }

            $validateResultado = $this->hasTurno($fecha);

            if ($validateResultado->hasError()) {
                return $validateResultado;
            }

            if (! count($errors)) {
                return new ValidateResultado($feriado, []);
            }

        }
        return new ValidateResultado($feriado, []);
    }

    /**
     * Validar si hay turnos en la fecha especificada
     *
     * @param date $fecha fecha a buscar
     * @return ValidateResultado
     */
    private function hasTurno($fecha)
    {
        $errors = [];
        $turno = $this->turnoRepository->findOneByFecha(array(
            'fecha' => $fecha->format('Y-m-d')
        ));

        if ($turno) {
            $errors[] = 'Existen turnos asignados para la fecha seleccionada.';
        }

        return new ValidateResultado(null, $errors);
    }

    /**
     * Validar fecha
     *
     * @param date $fecha fecha a validar
     * @return ValidateResultado
     */
    public function validarFecha($fecha)
    {
        $errors = [];
        $fecha = new \DateTime($fecha);

        $feriado = $this->feriadoNacionalRepository->findOneBy(['fecha' => $fecha]);

        if (! $feriado) {
            $errors[] = 'La fecha ingresada no corresponde a un Feriado Nacional.';
        }

        if (count($errors) > 0) {
            return new ValidateResultado(null, $errors);
        }
        return new ValidateResultado($feriado, []);
    }
}

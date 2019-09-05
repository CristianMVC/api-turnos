<?php
namespace ApiV1Bundle\Entity\Validator;

use ApiV1Bundle\Entity\ValidateResultado;
use Symfony\Component\Validator\Constraints\Null;

/**
 * Class HorarioAtencionValidator
 * @package ApiV1Bundle\Entity\Validator
 */

class HorarioAtencionValidator extends SNTValidator
{
    /**
     * Validamos los parámetros que vienen desde el front
     *
     * @param object $puntoAtencion objeto punto de atención
     * @param array $params arreglo con los datos a validar
     * @return ValidateResultado
     */
    public function validarParams($puntoAtencion, $params)
    {
        $errors = [];

        if (! $puntoAtencion) {
            $errors[] = 'Punto de Atención inexistente';
            return new ValidateResultado(null, $errors);
        }

        $errors = $this->validar($params, [
            'diasSemana' => 'required:matriz',
            'horaInicio' => 'required:time',
            'horaFin' => 'required:time'
        ]);

        return new ValidateResultado(null, $errors);
    }

    /**
     * Validamos horarios de atencion
     *
     * @param array $params arreglo con los datos a validar
     * @return ValidateResultado
     */
    public function validarHorarios($params)
    {
        $errors = [];
        /*
         * La hora de inicio y fin en la base de datos se almacena como TIME,
         * al obtenerlos nos devuelve un objeto DateTime con fecha 1970-01-01.
         *
         * Las horas que se reciben por parámetro se les asigna esa misma fecha
         * para poder compararlas.
         */
        $horaInicio = (new \DateTime($params['horaInicio']))->setDate(1970, 1, 1);
        $horaFin = (new \DateTime($params['horaFin']))->setDate(1970, 1, 1);
        if ($horaInicio >= $horaFin){
            $errors[] = 'La hora de inicio debe ser menor a la hora de fin';
        }
        return new ValidateResultado(null, $errors);
    }

    /**
     * Validamos que el Punto de atencion exista
     *
     * @param object $puntoAtencion Objeto puntoAtencion
     * @return ValidateResultado
     */
    public function validarPuntoAtencion($puntoAtencion)
    {
        $errors = [];
        if (! $puntoAtencion) {
            $errors[] = 'Punto de Atencion inexistente';
            return new ValidateResultado(null, $errors);
        }

        return new ValidateResultado($puntoAtencion, []);
    }
}

<?php
namespace TotemV1Bundle\Entity\Validator;

use ApiV1Bundle\Entity\Validator\SNTValidator;
use ApiV1Bundle\Entity\ValidateResultado;

class TurnoTotemValidator extends SNTValidator
{

    /**
     * Validar datos del turno
     *
     * @param array $params arreglo con los datos a validar (puntoatencion, tramite, grupo_tramite, nombre, apellido, cuil, prioridad )
     * @return ValidateResultado
     */
    public function validarTurno($params)
    {
        $cond = (isset($params['excepcional']) && $params['excepcional'])
            ? ''
            : ':cuil';

        $errors = $this->validar($params, [
            'puntoatencion' => 'required',
            'tramite' => 'required',
            'grupo_tramite' => 'required',
            'nombre' => 'required',
            'apellido' => 'required',
            'cuil' => 'required' . $cond,
            'prioridad' => 'required',
        ]);
        return new ValidateResultado($params, $errors);
    }
}

<?php

namespace ApiV1Bundle\Entity\Validator;

use ApiV1Bundle\Entity\User;
use ApiV1Bundle\Entity\Usuario;
use ApiV1Bundle\Entity\ValidateResultado;

/**
 * Class AreaValidator
 * @package ApiV1Bundle\Entity\Validator
 */
class AreaValidator extends SNTValidator
{
    /**
     * Valida los parámetros para crear un área
     *
     * @param array $params arreglo con los datos a validar
     * @return ValidateResultado
     */
    private function validarParams($params)
    {
        return $this->validar($params, [
            'nombre' => 'required',
            'abreviatura' => 'required'
        ]);
    }

    /**
     * Valida parámetros en la creación de un organismo
     *
     * @param array $params arreglo con los datos a validar
     * @return ValidateResultado
     */
    public function validarCreate($params, $organismo)
    {
        $errors = $this->validarParams($params);

        if (!$organismo) {
            $errors[] = 'Organismo inexistente';
        }

        return new ValidateResultado(null, $errors);
    }

    /**
     * Valida los parámetros en la edición de un área y qué tanto dicha área y
     * su organismo existan.
     *
     * @param object $area objeto área
     * @param object $organismo objeto organismo
     * @param array $params arreglo con los datos a validar
     * @return ValidateResultado
     */
    public function validarEdit($params, $area, $organismo)
    {
        $errors = $this->validarParams($params);

        if (!$area) {
            $errors[] = 'Area inexistente';
        }

        if (!$organismo) {
            $errors[] = 'Organismo inexistente';
        }

        return new ValidateResultado(null, $errors);
    }

    /**
     * Validar antes de borrar
     *
     * @param object $area objeto área
     * @param object $organismo objeto organismo
     * @return ValidateResultado
     */
    public function validarDelete($organismo, $area)
    {
        $errors = [];


        if (!$organismo) {
            $errors[] = 'Organismo inexistente';
            return new ValidateResultado(null, $errors);
        }

        if (!$area) {
            $errors[] = 'Area inexistente';
            return new ValidateResultado(null, $errors);
        }

        if ($area->getOrganismo()->getId() != $organismo->getId()) {
            $errors[] = 'El Area no pertenece al Organismo.';
        }

        if ($area->getPuntosAtencion()->count() > 0) {
            $errors[] = 'Existen puntos de atencion asociados al Area, la misma no puede ser borrada';
        }


        if ($area->getTramite()->count() > 0) {
            $errors[] = 'Existen tramites asociados al Area, la misma no puede ser borrada';
        }

        return new ValidateResultado(null, $errors);
    }

    /**
     * Validar parametros de la busqueda
     *
     * @param array $params arreglo con los datos a validar
     * @return ValidateResultado
     */
    public function validarBusquedaParams($params)
    {
        $errors = $this->validar($params, [
            'q' => 'required'
        ]);

        if (strlen($params['q']) < 3) {
            $errors[] = 'Ingrese un minimo de 3 caracteres para la busqueda';
        }
        return new ValidateResultado(null, $errors);
    }

    /**
     * Validar busqueda de areas
     *
     * @param object $area objeto Area
     * @return ValidateResultado
     */
    public function validarSearch($area)
    {
        $errors = [];
        if (!$area) {
            $errors[] = 'Área Inexistente';
            return new ValidateResultado(null, $errors);
        }

        return new ValidateResultado($area, []);
    }

}

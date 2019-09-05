<?php
namespace ApiV1Bundle\Entity\Validator;

use ApiV1Bundle\Entity\User;
use ApiV1Bundle\Entity\Usuario;
use ApiV1Bundle\Entity\ValidateResultado;

/**
 * Class OrganismoValidator
 * @package ApiV1Bundle\Entity\Validator
 */
class OrganismoValidator extends SNTValidator
{
    /**
     * Valida los parametros para crear un organismo
     *
     * @param array $params arreglo con los datos a validar
     * @return array
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
    public function validarCreate($params)
    {
        $errors = $this->validarParams($params);
        return new ValidateResultado(null, $errors);
    }

    /**
     * Valida los parámetros en la edición de un organismo y qué dicho organismo
     * exista.
     *
     * @param object $organismo objeto organismo
     * @param array $params arreglo con los datos a validar
     * @return ValidateResultado
     */
    public function validarEdit($organismo, $params)
    {
        $errors = $this->validarParams($params);

        if (! $organismo) {
            $errors[] = 'Organismo inexistente';
        }

        return new ValidateResultado(null, $errors);
    }

    /**
     * Valida que el organismo que se desea eliminar exista y no tenga áreas
     * asociadas
     *
     * @param object $organismo objeto organismo
     * @return ValidateResultado
     */
    public function validarDelete($organismo)
    {
        $errors = [];

        if (! $organismo) {
            $errors[] = 'Organismo inexistente';
            return new ValidateResultado(null, $errors);
        }

        if ($organismo->getAreas()->count() > 0) {
            $errors[] = 'Existen Areas asociadas al Organismo, el mismo no puede ser borrado';
        }

        return new ValidateResultado(null, $errors);
    }

    /**
     * Valida que el query de busqueda exista y tenga un mínimo de 3
     * carácteres
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
            $errors[] = 'Ingrese un minimo de 3 caracteres';
        }

        return new ValidateResultado(null, $errors);
    }

}

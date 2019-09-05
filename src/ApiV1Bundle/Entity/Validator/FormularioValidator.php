<?php

namespace ApiV1Bundle\Entity\Validator;

use ApiV1Bundle\Entity\ValidateResultado;

/**
 * Class FormularioValidator
 * @package ApiV1Bundle\Entity\Validator
 */
class FormularioValidator extends SNTValidator
{
    /**
     * Valida los parametros para crear un formulario
     *
     * @param array $params arreglo con los datos a validar
     * @return array
     */
    public function validarParams($params)
    {
        //Cambiar el formato del array para que sea key=>value
        $campos = array();
        $reglas = array();
        foreach ($params as $campo){
            $campos[$campo["key"]] = $campo["label"];
            $reglas[$campo["key"]] = 'etiqueta';
            if (isset($campo["formComponent"]["options"])){
                foreach ($campo["formComponent"]["options"] as $subcampo){
                    $campos[$campo["key"] . "." . $subcampo["key"]] = $subcampo["value"];
                    $reglas[$campo["key"] . "." . $subcampo["key"]] = 'etiqueta';
                }
            }
        }
        return $this->validar($campos, $reglas);
    }

    /**
     * Valida parámetros en la creación de un formulario
     *
     * @param array $params arreglo con los datos a validar
     * @return ValidateResultado
     */
    public function validarCreate($params)
    {
        $errors = $this->validarParams($params);
        foreach ($params as $campo){
            switch ($campo["type"]) {
                case "textbox":
                    if ( !isset($campo["formComponent"]["typeValue"]) || (empty($campo["formComponent"]["typeValue"])) ) {
                        $errors[] = 'En el campo ' . $campo["key"] .' se debe especificar el tipo de textbox';
                    }
                    break;
                case "textarea":
                    if ( !isset($campo["formComponent"]["rows"]) || (empty($campo["formComponent"]["rows"])) ) {
                        $errors[] = 'En el campo ' . $campo["key"] .' se debe especificar la cantidad de filas del textarea';
                    }
                    break;
                case "radio":
                case "dropdown":
                    if ( !isset($campo["formComponent"]["options"])  ) {
                        $errors[] = 'En el campo ' . $campo["key"] .' se debe especificar la lista de opciones';
                    }
                    break;
            }
        }
        return new ValidateResultado(null, $errors);
    }

    /**
     * Valida parámetros en la modificacion de un formulario
     *
     * @param array $params arreglo con los datos a validar
     * @return ValidateResultado
     */
    public function validarEdit($params)
    {
        $errors = $this->validarParams($params);
        foreach ($params as $campo){
            switch ($campo["type"]) {
                case "textbox":
                    if ( !isset($campo["formComponent"]["typeValue"]) || (empty($campo["formComponent"]["typeValue"])) ) {
                        $errors[] = 'En el campo ' . $campo["key"] .' se debe especificar el tipo de textbox';
                    }
                    break;
                case "textarea":
                    if ( !isset($campo["formComponent"]["rows"]) || (empty($campo["formComponent"]["rows"])) ) {
                        $errors[] = 'En el campo ' . $campo["key"] .' se debe especificar la cantidad de filas del textarea';
                    }
                    break;
                case "radio":
                case "dropdown":
                    if ( !isset($campo["formComponent"]["options"])  ) {
                        $errors[] = 'En el campo ' . $campo["key"] .' debe existir una lista de opciones';
                    }
                    break;
            }
        }
        return new ValidateResultado(null, $errors);
    }
}
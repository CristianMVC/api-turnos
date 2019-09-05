<?php
namespace TotemV1Bundle\Entity\Validator;

use ApiV1Bundle\Entity\Validator\SNTValidator;
use ApiV1Bundle\Entity\ValidateResultado;

class TramiteTotemValidator extends SNTValidator
{
    /**
     * Validar datos del tramite
     *
     * @param array $params arreglo con los datos a validar (puntoatencion y grupo_tramite  )
     * @return ValidateResultado
     */
    public function validarTramite($params)
    {
        $errors = $this->validar($params, [
            'puntoatencion' => 'required',
            'grupo_tramite' => 'required'
        ]);
        return new ValidateResultado($params, $errors);
    }
}
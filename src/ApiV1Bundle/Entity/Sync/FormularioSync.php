<?php

namespace ApiV1Bundle\Entity\Sync;

use ApiV1Bundle\Entity\ValidateResultado;
use ApiV1Bundle\Entity\Factory\FormularioFactory;
use ApiV1Bundle\Helper\FormHelper;
use ApiV1Bundle\Entity\Validator\FormularioValidator;

/**
 * Class FormularioSync
 * @package ApiV1Bundle\Entity\Sync
 *
 */

class FormularioSync
{
    /** @var FormularioValidator  */
    private $formularioValidator;

    /**
     * FormularioSync constructor.
     *
     * @param FormularioValidator $formularioValidator
     */
    public function __construct(FormularioValidator $formularioValidator)
    {
        $this->formularioValidator = new FormularioValidator();
    }

    /**
     * Modificación de un formulario. El FormularioSync valida los campos que vienen por params y modifica el formulario.
     *
     * @param object $formulario formulario a modificar
     * @param array $campos Arreglo con los campos del formulario
     * @return mixed
     */
    public function edit($formulario, $params)
    {
        $validateResultado = $this->formularioValidator->validarEdit($params);
        if (!$validateResultado->hasError()) {
            $formulario->setCampos(FormHelper::datosFormulario($params));
            return new ValidateResultado($formulario, []);
        }
        return $validateResultado;
    }
}

<?php
namespace ApiV1Bundle\Entity\Factory;

use ApiV1Bundle\Entity\Formulario;
use ApiV1Bundle\Helper\FormHelper;
use ApiV1Bundle\Entity\ValidateResultado;
use ApiV1Bundle\Entity\Validator\FormularioValidator;

/**
 * Class FormularioFactory
 * @package ApiV1Bundle\Entity\Factory
 */

class FormularioFactory
{
    /** @var FormularioValidator c */
    private $formularioValidator;

    /**
     * FormularioFactory constructor.
     * @param FormularioValidator $formularioValidator
     */
    public function __construct(FormularioValidator $formularioValidator)
    {
        $this->formularioValidator = $formularioValidator;
    }

    /**
     * Crea un formulario
     *
     * @param array $campos Array con los campos del formulario
     * @return mixed
     */

    public function create($campos)
    {
        $validateResultado = $this->formularioValidator->validarCreate($campos);
        if (!$validateResultado->hasError()) {
            $formulario = new Formulario(FormHelper::datosFormulario($campos));




            return new ValidateResultado($formulario, []);
        }
        return $validateResultado;

    }
}

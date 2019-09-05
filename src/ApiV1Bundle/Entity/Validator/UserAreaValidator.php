<?php
namespace ApiV1Bundle\Entity\Validator;

use ApiV1Bundle\Repository\UserRepository;
use ApiV1Bundle\Entity\ValidateResultado;

/**
 * Class UserAreaValidator
 * @package ApiV1Bundle\Entity\Validator
 */
class UserAreaValidator extends UserValidator
{
    /**
     * UserAreaValidator constructor.
     * @param UserRepository $userRepository
     * @param $encoder
     */
    public function __construct(UserRepository $userRepository, $encoder)
    {
        parent::__construct($userRepository, $encoder);
    }

    /**
     * Validar parametros para la creación de un usuario de area
     *
     * @param array $params array con datos a validar
     * @return ValidateResultado
     */
    public function validarCreate($params)
    {
        $validateResultado = $this->validarDatosBasicos($params);
        if (! $validateResultado->hasError()) {
            $validateResultado = $this->validarDuplicado($params['username']);
        }
        if (! $validateResultado->hasError()) {
            $errors = $this->validar($params, [
                'area' => 'required:integer'
            ]);
            return new ValidateResultado(null, $errors);
        }
        return $validateResultado;
    }

    /**
     * Validamos parametros para la edición de un user area
     *
     * @param array $params array con datos a validar
     * @return ValidateResultado
     */
    public function validarEdit($params, $userArea, $id = null)
    {
        $validateResultado = $this->validarDatosBasicos($params, $id);
        if (! $validateResultado->hasError()) {
            $validateResultado = $this->validarEntidad($userArea, 'Usuario inexistente');
        }
        if (! $validateResultado->hasError()) {
            $user = $userArea->getUser();
            if (isset($params['username']) && $params['username'] !== $user->getUsername()) {
                $validateResultado = $this->validarDuplicado($params['username']);
            }
        }
        return $validateResultado;
    }
}

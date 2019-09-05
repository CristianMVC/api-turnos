<?php
namespace ApiV1Bundle\Entity\Validator;

use ApiV1Bundle\Repository\UserRepository;
use ApiV1Bundle\Entity\ValidateResultado;

/**
 * Class AdminValidator
 * @package ApiV1Bundle\Entity\Validator
 */
class AdminValidator extends UserValidator
{
    /**
     * AdminValidator constructor.
     * @param UserRepository $userRepository
     * @param $encoder
     */
    public function __construct(UserRepository $userRepository, $encoder)
    {
        parent::__construct($userRepository, $encoder);
    }

    /**
     * Validar parametros para la creación de un Admin
     *
     * @param array $params arreglo con los datos a validar
     * @return ValidateResultado
     */
    public function validarCreate($params)
    {
        $validateResultado = $this->validarDatosBasicos($params);
        if (! $validateResultado->hasError()) {
            $validateResultado = $this->validarDuplicado($params['username']);
        }
        return $validateResultado;
    }

    /**
     * Validamos parametros para la edición de un admin
     *
     * @param array $params arreglo con los datos a validar
     * @return ValidateResultado
     */
    public function validarEdit($params, $admin, $id = null)
    {
        $validateResultado = $this->validarDatosBasicos($params, $id);
        if (! $validateResultado->hasError()) {
            $validateResultado = $this->validarEntidad($admin, 'Usuario inexistente');
        }
        if (! $validateResultado->hasError()) {
            $user = $admin->getUser();
            if (isset($params['username']) && $params['username'] !== $user->getUsername()) {
                $validateResultado = $this->validarDuplicado($params['username']);
            }
        }
        return $validateResultado;
    }
}

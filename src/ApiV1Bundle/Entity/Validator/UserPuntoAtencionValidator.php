<?php
namespace ApiV1Bundle\Entity\Validator;

use ApiV1Bundle\Entity\UserPuntoAtencion;
use ApiV1Bundle\Repository\UserRepository;
use ApiV1Bundle\Entity\ValidateResultado;

class UserPuntoAtencionValidator extends UserValidator
{
    public function __construct(UserRepository $userRepository, $encoder)
    {
        parent::__construct($userRepository, $encoder);
    }

    /**
     * Validar parametros para la creación de un usuario de puntoatencion
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
                'puntoAtencion' => 'required:integer'
            ]);
            return new ValidateResultado(null, $errors);
        }
        return $validateResultado;
    }

    /**
     * Validamos parametros para la edición de un user puntoatencion
     *
     * @param array $params array con datos a validar
     * @param UserPuntoAtencion $userPuntoAtencion
     * @param integer $id Identificador único de usuario
     * @return ValidateResultado
     */
    public function validarEdit($params, $userPuntoAtencion, $id = null)
    {
        $validateResultado = $this->validarDatosBasicos($params, $id);
        if (! $validateResultado->hasError()) {
            $validateResultado = $this->validarEntidad($userPuntoAtencion, 'Usuario inexistente');
        }
        if (! $validateResultado->hasError()) {
            $user = $userPuntoAtencion->getUser();
            if (isset($params['username']) && $params['username'] !== $user->getUsername()) {
                $validateResultado = $this->validarDuplicado($params['username']);
            }
        }
        return $validateResultado;
    }
}

<?php
namespace ApiV1Bundle\Entity\Validator;

use ApiV1Bundle\Entity\User;
use ApiV1Bundle\Entity\ValidateResultado;
use ApiV1Bundle\Repository\UserRepository;
use Lcobucci\JWT\Parsing\Encoder;

/**
 * Class UserValidator
 * @package ApiV1Bundle\Entity\Validator
 */
class UserValidator extends SNTValidator
{
    const EMAIL_DUPLICADO = 'Ya existe un usuario registrado con ese mail';
    /** @var UserRepository  */
    private $userRepository;
    /** @var Encoder */
    private $encoder;

    /**
     * UserValidator constructor.
     * @param UserRepository $userRepository
     * @param $encoder
     */
    public function __construct(UserRepository $userRepository, $encoder)
    {
        $this->userRepository = $userRepository;
        $this->encoder = $encoder;
    }

    /**
     * Validar que exista usuario y contraseña
     *
     * @param array $params array con datos a validar (username y contraseña)
     * @param object $user objeto User
     * @return ValidateResultado
     */
    public function validarParamsLogin($params, $user)
    {
        $errors = $this->validar($params, [
            'username' => 'required',
            'password' => 'required'
        ]);
        if (! count($errors) && ! $user) {
            $errors[] = 'Usuario/contraseña incorrectos';
        }
        return new ValidateResultado($user, $errors);
    }

    /**
     * Validar el login del usuario
     *
     * @param object $user objeto User
     * @param string $password contraseña
     * @return ValidateResultado
     */
    public function validarLogin($user, $password)
    {
        $errors = [];
        if (! $this->encoder->isPasswordValid($user, $password)) {
            $errors[] = 'Usuario/contraseña incorrectos';
        }
        return new ValidateResultado($user, $errors);
    }

    /**
     * Valida el login del usuario al modificar su contraseña
     *
     * @param object $user objeto User
     * @param string $password contraseña
     * @return ValidateResultado
     */
    public function validarModificarContrasena($user, $password)
    {
        $errors = [];
        if (! $this->encoder->isPasswordValid($user, $password)) {
            $errors['error'] = 'La contraseña actual es incorrecta';
        }
        return new ValidateResultado($user, $errors);
    }

    /**
     * Validar creación del usuario
     *
     * @param array $params array con datos a validar (rol, username y nombre)
     * @param integer $id identificador único de usuario
     * @return ValidateResultado
     */
    public function validarDatosBasicos($params, $id = null)
    {
        $rules = [
            'rol' => 'required',
            'username' => 'required:email',
            'nombre' => 'required'
        ];
        $errors = $this->validar($params, $rules);

        if (!$errors) {
            $user = $this->userRepository->findOneByUsername($params['username']);
            if ($user) {
                if ($id) {
                    if ($user->getId() != $id) {
                        $errors[] = UserValidator::EMAIL_DUPLICADO;
                    }
                } else {
                    $errors[] = UserValidator::EMAIL_DUPLICADO;
                }
            }
        }

        return new ValidateResultado(null, $errors);
    }

    /**
     * Valida si existe un usuario con ese username
     *
     * @param string $username username
     * @return ValidateResultado
     */
    public function validarDuplicado($username)
    {
        $errors = [];
        $user = $this->userRepository->findOneByUsername($username);
        if ($user) {
            $errors[] = 'Ya existe un usuario con el email ingresado';
        }
        return new ValidateResultado(null, $errors);
    }

    /**
     * Comprobar si una contraseña es valida
     *
     * @param string $password contraseña
     * @return bool
     */
    public function isValidPassword($password)
    {
        if (strlen($password) >= 8 && strlen($password) <= 15) {
            if (preg_match('/^[a-zA-Z0-9]+/', $password)) {
                return true;
            }
        }
        return false;
    }

    /**
     * Validamos que se envíe un usuario y que el usuario exista
     *
     * @param object $user objeto User
     * @param array $params array con datos a validar (username)
     * @return ValidateResultado
     */
    public function validarParamsRecuperar($params, $user)
    {
        $errors = $this->validar($params, [
            'username' => 'required'
        ]);
        if (! count($errors) && ! $user) {
            $errors[] = 'Usuario incorrecto';
        }
        return new ValidateResultado($user, $errors);
    }

    /**
     * Validar usuario y contraseña para modificar contraseña
     *
     * @param array $params array con datos a validar
     * @param string $token token del usuario logueado
     * @return ValidateResultado
     */
    public function validarModificarPassword($params, $token)
    {
        $errors = [];

        $user = $this->userRepository->findOneByUsername($params['username']);
        if (! $user) {
            $errors[] = 'No se encontró un usuario con el mail ingresado';
            return new ValidateResultado(null, $errors);
        }

        if (! $token->isValid()) {
            $errors[] = 'El token es inválido';
            return new ValidateResultado(null, $errors);
        }

        if (! $this->isValidPassword($params['nuevoPassword'])) {
            $errors[] = 'La contraseña no es válida. Debe tener entre 8 y 15 caracteres alfanuméricos';
            return new ValidateResultado(null, $errors);
        }

        return new ValidateResultado($user, []);
    }
}

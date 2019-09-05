<?php
namespace ApiV1Bundle\ApplicationServices;

use ApiV1Bundle\Entity\ValidateResultado;
use ApiV1Bundle\Entity\Validator\TokenValidator;
use Symfony\Component\DependencyInjection\Container;
use ApiV1Bundle\Repository\UserRepository;
use ApiV1Bundle\Helper\JWToken;
use ApiV1Bundle\Entity\Validator\UserValidator;
use ApiV1Bundle\Repository\UsuarioRepository;
use ApiV1Bundle\Repository\TokenRepository;
use ApiV1Bundle\Entity\Factory\TokenFactory;
use ApiV1Bundle\ExternalServices\SecurityIntegration;
use ApiV1Bundle\Entity\Validator\CommunicationValidator;

/**
 * Class SecurityServices
 * @package ApiV1Bundle\ApplicationServices
 */
class SecurityServices extends SNTServices
{
    /** @var UserRepository  */
    private $userRepository;
    /** @var TokenRepository  */
    private $tokenRepository;
    /** @var JWToken  */
    private $jwtoken;
    /** @var UserValidator  */
    private $userValidator;
    /** @var UsuarioRepository  */
    private $usuarioRepository;
    /** @var SecurityIntegration  */
    private $securityIntegration;
    /** @var CommunicationValidator  */
    private $communicationValidator;
    /** @var TokenValidator  */
    private $tokenValidator;

    /**
     * SecurityServices constructor.
     * @param Container $container
     * @param UserRepository $userRepository
     * @param TokenRepository $tokenRepository
     * @param JWToken $jwtoken
     * @param UserValidator $userValidator
     * @param UsuarioRepository $usuarioRepository
     * @param SecurityIntegration $securityIntegration
     * @param CommunicationValidator $communicationValidator
     * @param TokenValidator $tokenValidator
     */
    public function __construct(
        Container $container,
        UserRepository $userRepository,
        TokenRepository $tokenRepository,
        JWToken $jwtoken,
        UserValidator $userValidator,
        UsuarioRepository $usuarioRepository,
        SecurityIntegration $securityIntegration,
        CommunicationValidator $communicationValidator,
        TokenValidator $tokenValidator
    ) {
        parent::__construct($container);
        $this->userRepository = $userRepository;
        $this->tokenRepository = $tokenRepository;
        $this->jwtoken = $jwtoken;
        $this->userValidator = $userValidator;
        $this->usuarioRepository = $usuarioRepository;
        $this->securityIntegration = $securityIntegration;
        $this->communicationValidator = $communicationValidator;
        $this->tokenValidator = $tokenValidator;
    }

    /**
     * User login
     *
     * @param array $params Parametros para el login
     * @param callback $onError Callback para devolver respuesta fallida
     * @return mixed
     */
    public function login($params, $onError)
    {
        $username = isset($params['username']) ? $params['username'] : null;
        $result = [];
        $user = $this->userRepository->findOneByUsername($username);
        $usuario = $this->usuarioRepository->findOneByUser($user);
        $validateResult = $this->userValidator->validarParamsLogin($params, $user);
        if (! $validateResult->hasError()) {
            $validateResult = $this->userValidator->validarLogin($user, $params['password']);
            if (! $validateResult->hasError()) {
                $result = [
                    'id' => $user->getId(),
                    'username' => $user->getUsername(),
                    'token' => $this->jwtoken->getToken($user->getId(), $user->getUsername(), $user->getRoles()),
                    'organismo' => $usuario->getOrganismoId(),
                    'area' => $usuario->getAreaId(),
                    'rol' => $user->getRoles(),
                    'rol_id' => $user->getRol(),
                    'nombre' => $usuario->getNombre(),
                    'apellido' => $usuario->getApellido(),
                    'puntoAtencion' => $usuario->getPuntoAtencionData()
                ];
            }
        }
        return $this->processResult(
            $validateResult,
            function ($entity) use ($result) {
                return $result;
            },
            $onError
        );
    }

    /**
     * User logout
     *
     * @param string $authorization token del usuario logueado
     * @param callback $success Callback para devolver respuesta exitosa
     * @param callback $error Callback para devolver respuesta fallida
     * @return mixed
     */
    public function logout($authorization, $success, $error)
    {
        // validamos el token
        if ($this->validarToken($authorization)) {
            $token = md5($authorization);
            // agregamos el token a la lista si no existe
            $verificarCancelado = $this->tokenRepository->findOneByToken($token);
            if (! $verificarCancelado) {
                $tokenFactory = new TokenFactory($this->tokenRepository);
                $validateResult = $tokenFactory->insert($token);
                return $this->processResult(
                    $validateResult,
                    function ($entity) use ($success) {
                        return call_user_func($success, $this->tokenRepository->save($entity));
                    },
                    $error
                );
            }
        }
        return call_user_func($success, []);
    }

    /**
     * Valida un token
     *
     * @param string $authorization token del usuario logueado
     * @return mixed
     */
    public function validarToken($authorization)
    {
        return $this->tokenValidator->validarAuthorization($authorization);
    }

    /**
     * Obtener token
     *
     * @param object $user usuario
     * @return \Lcobucci\JWT\Builder
     */
    public function getTokenByUser($user)
    {
        return $this->jwtoken->getToken($user->getId(), $user->getUsername(), $user->getRoles());
    }

    /**
     * Validación de token
     *
     * @param string $authorization token del usuario logueado
     * @param callback $success Callback para devolver respuesta exitosa
     * @param callback $onError Callback para devolver respuesta fallida
     * @return mixed
     */
    public function isTokenValid($authorization, $success, $onError)
    {
        $validateResult = $this->tokenValidator->validarToken($authorization);
        $user = $validateResult->getEntity();

        return $this->processResult(
            $validateResult,
            function () use ($success, $user) {
                return call_user_func($success, $user);
            },
            $onError
        );


    }

    /**
     * Validamos la comunicación entre APIs
     *
     * @param array $params  arreglo con los datos para probar la comunicación
     * @return mixed
     */
    public function sendSecurePostCommunication($params)
    {
        $response = $this->securityIntegration->securePostCommunications($params);
        foreach ($response as $key => $value) {
            if (is_object($value)) {
                $response->{$key} = (array) $value;
            }
        }
        return (array) $response;
    }

    /**
     * Validamos la comunicación con la API del SNC
     *
     * @param array $params  arreglo con los datos para probar la comunicación
     * @param callback $success Callback para devolver respuesta exitosa
     * @param callback $onError Callback para devolver respuesta fallida
     * @return mixed
     */
    public function validateSNCCommunication($params, $success, $onError)
    {
        $validateResult = $this->communicationValidator->validateSNCRequest($params);
        return $this->processResult(
            $validateResult,
            function () use ($success, $params) {
                return call_user_func($success, $params);
            },
            $onError
        );
    }
    
        /**
     * Validación de token
     *
     * @param string $authorization token del usuario logueado
     * @return mixed
     */
    public function validToken($authorization)
    {
        $validateResult = $this->tokenValidator->validarToken($authorization);
        $user = $validateResult->getEntity();
        
        if($user){
            return $user;
        }
        return null;
    }    
}

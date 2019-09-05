<?php

namespace ApiV1Bundle\ApplicationServices;

use ApiV1Bundle\Entity\Factory\TokenFactory;
use ApiV1Bundle\Entity\Factory\UsuarioFactoryStrategy;
use ApiV1Bundle\Entity\Sync\UsuarioSyncStrategy;
use ApiV1Bundle\Entity\User;
use ApiV1Bundle\Entity\UsuarioStrategy;
use ApiV1Bundle\Entity\Validator\AdminValidator;
use ApiV1Bundle\Entity\Validator\UserAreaValidator;
use ApiV1Bundle\Entity\Validator\UserOrganismoValidator;
use ApiV1Bundle\Entity\Validator\UserPuntoAtencionValidator;
use ApiV1Bundle\Entity\Validator\UserValidator;
use ApiV1Bundle\ExternalServices\NotificationsExternalService;
use ApiV1Bundle\Repository\AdminRepository;
use ApiV1Bundle\Repository\AreaRepository;
use ApiV1Bundle\Repository\OrganismoRepository;
use ApiV1Bundle\Repository\PuntoAtencionRepository;
use ApiV1Bundle\Repository\TokenRepository;
use ApiV1Bundle\Repository\UserAreaRepository;
use ApiV1Bundle\Repository\UserOrganismoRepository;
use ApiV1Bundle\Repository\UserPuntoAtencionRepository;
use ApiV1Bundle\Repository\UserRepository;
use ApiV1Bundle\Repository\UsuarioRepository;
use Symfony\Component\DependencyInjection\Container;
use Symfony\Component\Security\Core\Encoder\UserPasswordEncoder;

/**
 * Class UsuarioServices
 * @package ApiV1Bundle\ApplicationServices
 */
class UsuarioServices extends SNTServices
{
    /** @var UserPasswordEncoder  */
    private $encoder;
    /** @var UserRepository  */
    private $userRepository;
    /** @var UserValidator  */
    private $userValidator;
    /** @var AdminRepository  */
    private $adminRepository;
    /** @var AdminValidator  */
    private $adminValidator;
    /** @var UserOrganismoRepository  */
    private $userOrganismoRepository;
    /** @var UserOrganismoValidator  */
    private $userOrganismoValidator;
    /** @var UserAreaRepository  */
    private $userAreaRepository;
    /** @var UserAreaValidator  */
    private $userAreaValidator;
    /** @var UserPuntoAtencionRepository  */
    private $userPuntoAtencionRepository;
    /** @var UserPuntoAtencionValidator  */
    private $userPuntoAtencionValidator;
    /** @var OrganismoRepository  */
    private $organismoRepository;
    /** @var AreaRepository  */
    private $areaRepository;
    /** @var PuntoAtencionRepository  */
    private $puntoAtencionRepository;
    /** @var NotificationsExternalService  */
    private $notificationsServices;
    /** @var UsuarioRepository  */
    private $usuarioRepository;
    /** @var SecurityServices  */
    private $securityService;
    /** @var RolesServices  */
    private $rolesServices;
    /** @var TokenRepository  */
    private $tokenRepository;

    /**
     * UsuarioServices constructor.
     * @param Container $container
     * @param UserPasswordEncoder $encoder
     * @param UserRepository $userRepository
     * @param UserValidator $userValidator
     * @param AdminRepository $adminRepository
     * @param AdminValidator $adminValidator
     * @param UserOrganismoRepository $userOrganismoRepository
     * @param UserOrganismoValidator $userOrganismoValidator
     * @param UserAreaRepository $userAreaRepository
     * @param UserAreaValidator $userAreaValidator
     * @param UserPuntoAtencionRepository $userPuntoAtencionRepository
     * @param UserPuntoAtencionValidator $userPuntoAtencionValidator
     * @param OrganismoRepository $organismoRepository
     * @param AreaRepository $areaRepository
     * @param PuntoAtencionRepository $puntoAtencionRepository
     * @param NotificationsExternalService $notificationsServices
     * @param UsuarioRepository $usuarioRepository
     * @param SecurityServices $securityServices
     * @param RolesServices $rolesServices
     * @param TokenRepository $tokenRepository
     */
    public function __construct(
        Container $container,
        UserPasswordEncoder $encoder,
        UserRepository $userRepository,
        UserValidator $userValidator,
        AdminRepository $adminRepository,
        AdminValidator $adminValidator,
        UserOrganismoRepository $userOrganismoRepository,
        UserOrganismoValidator $userOrganismoValidator,
        UserAreaRepository $userAreaRepository,
        UserAreaValidator $userAreaValidator,
        UserPuntoAtencionRepository $userPuntoAtencionRepository,
        UserPuntoAtencionValidator $userPuntoAtencionValidator,
        OrganismoRepository $organismoRepository,
        AreaRepository $areaRepository,
        PuntoAtencionRepository $puntoAtencionRepository,
        NotificationsExternalService $notificationsServices,
        UsuarioRepository $usuarioRepository,
        SecurityServices $securityServices,
        RolesServices $rolesServices,
        TokenRepository $tokenRepository
    ) {
        parent::__construct($container);
        $this->encoder = $encoder;
        $this->userRepository = $userRepository;
        $this->userValidator = $userValidator;
        $this->adminRepository = $adminRepository;
        $this->adminValidator = $adminValidator;
        $this->userOrganismoRepository = $userOrganismoRepository;
        $this->userOrganismoValidator = $userOrganismoValidator;
        $this->userAreaRepository = $userAreaRepository;
        $this->userAreaValidator = $userAreaValidator;
        $this->userPuntoAtencionRepository = $userPuntoAtencionRepository;
        $this->userPuntoAtencionValidator = $userPuntoAtencionValidator;
        $this->organismoRepository = $organismoRepository;
        $this->areaRepository = $areaRepository;
        $this->puntoAtencionRepository = $puntoAtencionRepository;
        $this->notificationsServices = $notificationsServices;
        $this->usuarioRepository = $usuarioRepository;
        $this->securityService = $securityServices;
        $this->rolesServices = $rolesServices;
        $this->tokenRepository = $tokenRepository;
    }

    /**
     * Devuelve un usuario dado un Token
     *
     * @param string $authorization token del usuario logueado
     * @return mixed
     */
    public function getUsuarioByToken($authorization)
    {
        $validateResultado = $this->rolesServices->getUsuario($authorization);
        return $validateResultado->getEntity();
    }

    /**
     * Obtener el listado de usuarios
     *
     * @param integer $limit Cantidad máxima de registros a retornar
     * @param integer $offset Cantidad de registros a saltar
     * @param string $authorization token del usuario logueado
     * @param callback $onError Callback para devolver respuesta fallida
     * @param array $params 
     * @return mixed
     */
    public function findAllPaginate($limit, $offset, $authorization, $onError, $params = [])
    {
        $validateResultado = $this->rolesServices->getUsuario($authorization);
        $result = [];
        $resultset = [];

        if (! $validateResultado->hasError()) {
            $usuarios = $this->usuarioRepository->findAllPaginate($offset, $limit, $validateResultado->getEntity(), $params);
            foreach ($usuarios as $usuario) {
                $result[] = [
                    'id' => $usuario->getUser()->getId(),
                    'nombre' => $usuario->getNombre(),
                    'apellido' => $usuario->getApellido(),
                    'usuario' => $usuario->getUser()->getUsername(),
                    'rol' => $usuario->getUser()->getRol(),
                    'organismo' => $usuario->getOrganismoData(),
                    'area' => $usuario->getAreaData(),
                    'puntoAtencion' => $usuario->getPuntoAtencionData()
                ];
            }

            $resultset = [
                'resultset' => [
                    'count' => $this->usuarioRepository->getTotal($validateResultado->getEntity(), $params),
                    'offset' => $offset,
                    'limit' => $limit
                ]
            ];
        }

        return $this->processError(
            $validateResultado,
            function () use ($result, $resultset) {
                return $this->respuestaData($resultset, $result);
            },
            $onError
        );
    }

    /**
     * Obtener un usuario
     *
     * @param integer $id identificador único de usuario
     * @param callback $onError Callback para devolver respuesta fallida
     * @return object
     */
    public function get($id, $onError)
    {
        $result = [];
        $user = $this->userRepository->find($id);
        $validateResultado = $this->userValidator->validarEntidad($user, 'El usuario no existe');

        if (!$validateResultado->hasError()) {
            $usuarioRepository = new UsuarioStrategy(
                $this->adminRepository,
                $this->userOrganismoRepository,
                $this->userAreaRepository,
                $this->userPuntoAtencionRepository
            );
            $result = $usuarioRepository->getUser($user);

        }

        return $this->processError(
            $validateResultado,
            function () use ($result) {
                return $this->respuestaData([], $result);
            },
            $onError
        );
    }

    /**
     * Crear nuevo usuario
     *
     * @param array $params arreglo con los datos del usuario
     * @param callback $onSuccess Callback para devolver respuesta exitosa
     * @param callback $onError Callback para devolver respuesta fallida
     * @return mixed
     */
    public function create($params, $onSuccess, $onError)
    {



        $validateResult = $this->userValidator->validarDatosBasicos($params);
        $repository = null;
        $userData = null;

        if (!$validateResult->hasError()) {
            $usuarioFactory = new UsuarioFactoryStrategy(
                $this->adminRepository,
                $this->adminValidator,
                $this->userOrganismoRepository,
                $this->userOrganismoValidator,
                $this->userAreaRepository,
                $this->userAreaValidator,
                $this->userPuntoAtencionRepository,
                $this->userPuntoAtencionValidator,
                $this->organismoRepository,
                $this->areaRepository,
                $this->puntoAtencionRepository,
                $params['rol']
            );
            $repository = $usuarioFactory->getRepository();
            $validateResult = $usuarioFactory->create($params);

            $userData = [
                'title' => '¡Usuario creado con éxito!',
                'email' => null,
                'password' => null,
                'base_url' => $this->getParameter('usuarios_base_url')
            ];

            // securizar contraseña
            if (!$validateResult->hasError()) {
                $user = $validateResult->getEntity()->getUser();
                // user data
                $userData['email'] = $user->getUsername();
                $userData['password'] = $user->getPassword();
                // make the password secure
                $usuarioFactory->securityPassword($user, $this->getSecurityPassword());
            }
        }

        return $this->processResult(
            $validateResult,
            function ($entity) use ($onSuccess, $repository, $userData) {
                return call_user_func_array($onSuccess, [$repository->save($entity), $userData]);
            },
            $onError
        );
    }

    /**
     * Editar usuario
     *
     * @param array $params arreglo con los datos del usuario
     * @param integer $idUser identificador único de usuario a editar
     * @param callback $onSuccess Callback para devolver respuesta exitosa
     * @param callback $onError Callback para devolver respuesta fallida
     * @return mixed
     */
    public function edit($params, $idUser, $onSuccess, $onError)
    {
        $user = $this->userRepository->find($idUser);
        $validateResult = $this->userValidator->validarEntidad($user, 'Usuario inexistente');
        $repository = null;
        $userData = null;
        if (!$validateResult->hasError()) {
            // @Todo: Hay que validar que no exista otro usuario con el nuevo username
            if ($user->getUsername() != $params['username']) {
                $userData = [
                    'title' => '¡Usuario modificado con éxito!',
                    'email' => $params['username'],
                    'password' => "Misma contraseña",
                    'base_url' => $this->getParameter('usuarios_base_url')
                ];
            }

            $userSync = new UsuarioSyncStrategy(
                $this->adminRepository,
                $this->adminValidator,
                $this->userOrganismoRepository,
                $this->userOrganismoValidator,
                $this->userAreaRepository,
                $this->userAreaValidator,
                $this->userPuntoAtencionRepository,
                $this->userPuntoAtencionValidator,
                $this->organismoRepository,
                $this->areaRepository,
                $this->puntoAtencionRepository,
                $user->getRol()
            );
            $repository = $userSync->getRepository();
            $validateResult = $userSync->edit($idUser, $params);
        }

        return $this->processResult(
            $validateResult,
            function () use ($onSuccess, $repository, $userData) {
                return call_user_func($onSuccess, $userData, $repository->flush());
            },
            $onError
        );
    }

    /**
     * Eliminar usuario
     *
     * @param integer $idUser identificador único de usuario a editar
     * @param callback $onSuccess Callback para devolver respuesta exitosa
     * @param callback $onError Callback para devolver respuesta fallida
     * @return mixed
     */
    public function delete($idUser, $onSuccess, $onError)
    {
        $user = $this->userRepository->find($idUser);
        $validateResult = $this->userValidator->validarEntidad($user, 'Usuario inexistente');
        $repository = null;
        if (!$validateResult->hasError()) {
            $userSync = new UsuarioSyncStrategy(
                $this->adminRepository,
                $this->adminValidator,
                $this->userOrganismoRepository,
                $this->userOrganismoValidator,
                $this->userAreaRepository,
                $this->userAreaValidator,
                $this->userPuntoAtencionRepository,
                $this->userPuntoAtencionValidator,
                $this->organismoRepository,
                $this->areaRepository,
                $this->puntoAtencionRepository,
                $user->getRol()
            );
            $repository = $userSync->getRepository();
            $validateResult = $userSync->delete($idUser);
        }

        return $this->processResult(
            $validateResult,
            function ($entity) use ($onSuccess, $repository) {
                return call_user_func($onSuccess, $repository->remove($entity));
            },
            $onError
        );
    }

    /**
     * Enviar mensaje al usuario
     *
     * @param array $userData arreglo con los datos del usuario que se le enviará el mail
     * @param string $template nombre del template
     * @return mixed
     */
    public function enviarEmailUsuario($userData, $template)
    {

        try {
            return $this->notificationsServices->enviarNotificacion(
                $this->notificationsServices->getEmailTemplate($template),
                $userData['email'],
                '20359715286',
                $userData
            );
        } catch (\Exception $exception) {
            return $exception->getMessage();
        }
    }

    /**
     * Modifica la contraseña de un Usuario
     *
     * @param array $params arreglo con los datos del usuario (username,contraseña)
     * @param string $authorization token del usuario logueado
     * @param object $securityServices objeto SecurityServices para validar el token
     * @param callback $onSuccess Callback para devolver respuesta exitosa
     * @param callback $onError Callback para devolver respuesta fallida
     * @return mixed
     */
    public function modificarPassword($params, $authorization, $securityServices, $onSuccess, $onError)
    {
        $userData = [];
        $repository = $this->userRepository;
        $tokenString = md5($authorization);

        // validamos el token
        $token = $securityServices->validarToken($authorization);
        $validateResult = $this->userValidator->validarModificarPassword($params, $token);

        if (!$validateResult->hasError()) {
            $user = $validateResult->getEntity();

            if (isset($params['password'])) {
                $validateResult = $this->userValidator->validarModificarContrasena($user, $params['password']);
            }

            if (!$validateResult->hasError()) {
                $validateResult->setEntity($user);
                $userData = [
                    'title' => '¡Contraseña modificada con éxito!',
                    'email' => $user->getUsername(),
                    'password' => $params['nuevoPassword']
                ];
                // make the password secure
                $user->setPassword($this->encoder->encodePassword($user, $userData['password']));
            }
            $tokenFactory = new TokenFactory($this->tokenRepository);
            $validateToken = $tokenFactory->insert($tokenString);
            $token = $validateToken->getEntity();
        }

        return $this->processResult(
            $validateResult,
            function () use ($token, $onSuccess, $repository, $userData) {
                return call_user_func_array($onSuccess, [$repository->flush(), $this->tokenRepository->save($token), $userData]);
            },
            $onError
        );
    }

    /**
     * Envía un mail para recuperar contraseña
     *
     * @param array $params arreglo con los datos del usuario (username)
     * @param callback $onSuccess Callback para devolver respuesta exitosa
     * @param callback $onError Callback para devolver respuesta fallida
     * @return mixed
     */
    public function envioRecuperarPassword($params, $onSuccess, $onError)
    {
        $response = null;
        $user = $this->userRepository->findOneByUsername($params['username']);
        $validateResult = $this->userValidator->validarEntidad($user, 'Usuario inexistente');

        if (!$validateResult->hasError()) {
            $usuario = $this->usuarioRepository->findOneByUser($user->getId());
            $token = $this->securityService->getTokenByUser($user);

            $userData = [
                'url' => $this->getParameter('usuarios_base_url') . '/reset/' . $token,
                'nombre' => $usuario->getNombre(),
                'email' => $user->getUsername()
            ];

            $response = $this->enviarEmailUsuario($userData, 'recuperar-password');
        }

        return $this->processResult(
            $validateResult,
            function () use ($onSuccess, $response) {
                return call_user_func($onSuccess, $response);
            },
            $onError
        );
    }
}

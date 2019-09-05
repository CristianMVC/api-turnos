<?php
namespace ApiV1Bundle\Controller;

use ApiV1Bundle\ApplicationServices\SecurityServices;
use ApiV1Bundle\ApplicationServices\UsuarioServices;
use FOS\RestBundle\Controller\Annotations\Route;
use FOS\RestBundle\Controller\Annotations\Get;
use FOS\RestBundle\Controller\Annotations\Post;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\JsonResponse;
use Nelmio\ApiDocBundle\Annotation\ApiDoc;

/**
 * Class SecurityController
 * @package ApiV1Bundle\Controller
 */
class SecurityController extends ApiController
{
    /** @var SecurityServices */
    private $securityServices;
    /** @var  UsuarioServices */
    private $usuarioServices;

    /**
     * User login
     * @ApiDoc(section="Seguridad",
     *   parameters={
     *      {"name"="username", "dataType"="string", "required"=true, "description"="Nombre de usuario"},
     *      {"name"="password", "dataType"="string", "required"=true, "description"="Contraseña de usuario"}
     *  })
     * @param Request $request Datos necesarios para ejecutar la petición
     * @return mixed
     * @Post("/auth/login")
     */
    public function login(Request $request)
    {
        $params = $request->request->all();
        $this->securityServices = $this->getSecurityServices();
        return $this->securityServices->login(
            $params,
            function ($err) {
                return $this->respuestaForbidden($err);
            }
        );
    }

    /**
     * User logout
     * @ApiDoc(section="Seguridad")
     * @param Request $request Datos necesarios para ejecutar la petición
     * @return mixed
     * @Post("/auth/logout")
     */
    public function logout(Request $request)
    {
        $token = $request->headers->get('authorization', null);
        $this->securityServices = $this->getSecurityServices();
        return $this->securityServices->logout(
            $token,
            function ($token) {
                return $this->respuestaOk('Sesion terminada');
            },
            function ($error) {
                return $this->respuestaError($error);
            }
        );
    }

    /**
     * Recuperar contraseña del usuario
     * @ApiDoc(section="Seguridad",
     *   parameters={
     *      {"name"="username", "dataType"="string", "required"=true, "description"="Nombre de usuario"}
     *  })
     * @param Request $request Datos necesarios para ejecutar la petición
     * @return mixed
     * @Post("auth/reset")
     */
    public function envioRecuperarPassword(Request $request)
    {
        $params = $request->request->all();
        $this->usuarioServices = $this->getUsuarioServices();
        return $this->usuarioServices->envioRecuperarPassword(
            $params,
            function ($response) {
                return $this->respuestaOk(
                    'Se ha enviado un email a su casilla para recuperar la contraseña',
                    ['response' => $response]
                );
            },
            function ($err) {
                return $this->respuestaError($err);
            }
        );
    }

    /**
     * Valida si un token es valido o no
     * @ApiDoc(section="Seguridad")
     * @param Request $request Datos necesarios para ejecutar la petición
     * @return mixed
     * @Post("auth/validar")
     */
    public function validarTokenAction(Request $request)
    {
        $token = $request->headers->get('authorization', null);
        $this->securityServices = $this->getSecurityServices();
        return $this->securityServices->isTokenValid(
            $token,
            function ($user) {
                return $this->respuestaOk(
                    'Token valido',
                    ['username' => $user->getUsername()]
                );
            },
            function ($error) {
                return $this->respuestaError($error);
            }
        );
    }

    /**
     * Modificar contraseña del usuario
     * @ApiDoc(section="Seguridad",
     *   parameters={
     *      {"name"="username", "dataType"="string", "required"=true, "description"="Nombre de usuario"},
     *      {"name"="password", "dataType"="string", "required"=true, "description"="Contraseña de usuario"},
     *      {"name"="nuevoPassword", "dataType"="string", "required"=true, "description"="Nueva contraseña"}
     *  })
     * @param Request $request Datos necesarios para ejecutar la petición
     * @return mixed
     * @Post("auth/modificar")
     */
    public function modificarPassword(Request $request)
    {
        $params = $request->request->all();
        $authorization = $request->headers->get('authorization', null);
        $this->usuarioServices = $this->getUsuarioServices();
        return $this->usuarioServices->modificarPassword(
            $params,
            $authorization,
            $this->getSecurityServices(),
            function ($result, $userData) {
                return $this->respuestaOk('Contraseña modificada con éxito');
            },
            function ($err) {
                return $this->respuestaError($err);
            }
        );
    }

    /**
     * POST simple Test
     *
     * @param Request $request Datos necesarios para ejecutar la petición
     * @return mixed
     * @Post("/auth/test")
     */
    public function validatePostSimplePath(Request $request)
    {
        return [
            'Let me know if you can see this!'
        ];
    }

    /**
     * POST Test comunicación segura entre API's - request
     *
     * @param Request $request Datos necesarios para ejecutar la petición
     * @return mixed
     * @Post("/integration/secure/request")
     */
    public function validateRequestApiCommunication(Request $request)
    {
        $params = $request->request->all();
        $this->securityServices = $this->getSecurityServices();
        return $this->securityServices->sendSecurePostCommunication($params);
    }

    /**
     * POST Test comunicación segura entre API's - response
     *
     * @param Request $request Datos necesarios para ejecutar la petición
     * @return mixed
     * @Post("/integration/secure/response")
     */
    public function validateResponseApiCommunication(Request $request)
    {
        $params = $request->request->all();
        $this->securityServices = $this->getSecurityServices();
        return $this->securityServices->validateSNCCommunication(
            $params,
            function ($response) {
                return $this->respuestaOk('secure communication ok', $response);
            },
            function ($error) {
                return $this->respuestaError($error);
            }
        );
    }
}

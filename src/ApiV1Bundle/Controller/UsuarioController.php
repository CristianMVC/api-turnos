<?php
namespace ApiV1Bundle\Controller;

use ApiV1Bundle\ApplicationServices\UsuarioServices;
use FOS\RestBundle\Controller\Annotations as Rest;
use FOS\RestBundle\Controller\Annotations\Get;
use FOS\RestBundle\Controller\Annotations\Post;
use FOS\RestBundle\Controller\Annotations\Put;
use FOS\RestBundle\Controller\Annotations\Delete;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Nelmio\ApiDocBundle\Annotation\ApiDoc;

/**
 * Class UsuarioController
 * @package ApiV1Bundle\Controller
 */
class UsuarioController extends ApiController
{
    /** @var UsuarioServices */
    private $usuarioServices;

    /**
     * Listado de usuarios
     * @ApiDoc(section="Usuario",
     * parameters={
     *      {"name"="offset", "dataType"="integer", "required"=false},
     *      {"name"="limit", "dataType"="integer", "required"=false}
     *  })
     * @param Request $request Datos necesarios para ejecutar la petición
     * @return mixed
     * @Get("/usuarios")
     */
    public function getListAction(Request $request)
    {
        $authorization = $request->headers->get('Authorization', null);
        $offset = $request->get('offset', 0);
        $limit = $request->get('limit', 10);
        $params = $request->query->all();
        $this->usuarioServices = $this->getUsuarioServices();
        return $this->usuarioServices->findAllPaginate(
            (int) $limit,
            (int) $offset,
            $authorization,
            function ($err) {
                return $this->respuestaError($err);
            },
            $params
        );
    }

    /**
     * Obtiene un usuario
     * @ApiDoc(section="Usuario")
     * @param integer $idUser Identificador único de usuario
     * @return mixed
     * @Get("/usuarios/{idUser}")
     */
    public function getItemAction($idUser)
    {
        $this->usuarioServices = $this->getUsuarioServices();
        return $this->usuarioServices->get(
            $idUser,
            function ($err) {
                return $this->respuestaError($err);
            });
    }

    /**
     * Crear un usuario
     * @ApiDoc(section="Usuario",
     * parameters={
     *      {"name"="nombre", "dataType"="string", "required"=true, "description"="Nombre de usuario"},
     *      {"name"="apellido", "dataType"="string", "required"=true, "description"="Apellido del usuario"},
     *      {"name"="username", "dataType"="string", "required"=true, "description"="Email del usuario"},
     *      {"name"="rol", "dataType"="integer", "required"=true, "description"="Id rol"},
     *      {"name"="area", "dataType"="integer", "required"=false, "description"="Id area"},
     *      {"name"="organismo", "dataType"="integer", "required"=false, "description"="Id organismo"},
     *      {"name"="puntoatencion", "dataType"="integer", "required"=false, "description"="Id puntoatencion"}
     *  })
     * @param Request $request Datos necesarios para ejecutar la petición
     * @return mixed
     * @Post("/usuarios")
     */
    public function postAction(Request $request)
    {
        $params = $request->request->all();
        $usuarioServices = $this->getUsuarioServices();

        return $usuarioServices->create(
            $params,
            function ($usuario, $userdata) use ($usuarioServices) {

                return $this->respuestaOk('Usuario creado con éxito', [
                    'id' => $usuario->getUser()->getId(),
                    'response' => $usuarioServices->enviarEmailUsuario($userdata, 'usuario')
                ]);
            },
            function ($err) {
                return $this->respuestaError($err);
            }
        );
    }

    /**
     * Editar un usuario
     * @ApiDoc(section="Usuario",
     * parameters={
     *      {"name"="nombre", "dataType"="string", "required"=true, "description"="Nombre de usuario"},
     *      {"name"="apellido", "dataType"="string", "required"=true, "description"="Apellido del usuario"},
     *      {"name"="username", "dataType"="string", "required"=true, "description"="Email del usuario"},
     *      {"name"="rol", "dataType"="integer", "required"=true, "description"="Id rol"},
     *      {"name"="area", "dataType"="integer", "required"=false, "description"="Id area"},
     *      {"name"="organismo", "dataType"="integer", "required"=false, "description"="Id organismo"},
     *      {"name"="puntoatencion", "dataType"="integer", "required"=false, "description"="Id puntoatencion"}
     *  })
     * @param integer $idUser identificador del usuario
     * @param Request $request Datos necesarios para ejecutar la petición
     * @return mixed
     * @Put("/usuarios/{idUser}")
     */
    public function putAction(Request $request, $idUser)
    {
        $params = $request->request->all();
        $usuarioServices = $this->getUsuarioServices();

        return $usuarioServices->edit(
            $params,
            $idUser,
            function ($userdata) use ($usuarioServices) {
                if ($userdata) {
                    $usuarioServices->enviarEmailUsuario($userdata, 'usuario');
                }
                return $this->respuestaOk('Usuario modificado con éxito');
            },
            function ($err) {
                return $this->respuestaError($err);
            }
        );
    }

    /**
     * Eliminar un usuario
     * @ApiDoc(section="Usuario")
     * @param integer $id Identificador único del usuario
     * @return mixed
     * @Delete("/usuarios/{id}")
     */
    public function deleteAction($id)
    {
        $this->usuarioServices = $this->getUsuarioServices();
        return $this->usuarioServices->delete(
            $id,
            function () {
                return $this->respuestaOk('Usuario eliminado con éxito');
            },
            function ($err) {
                return $this->respuestaError($err);
            }
        );
    }
}

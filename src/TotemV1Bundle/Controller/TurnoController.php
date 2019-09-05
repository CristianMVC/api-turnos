<?php
namespace TotemV1Bundle\Controller;

use FOS\RestBundle\Controller\Annotations\Post;
use FOS\RestBundle\Controller\Annotations\Route;
use Symfony\Component\HttpFoundation\Request;


/**
 * Class TramiteController
 * @package TotemV1Bundle\Controller8
 */
class TurnoController extends ApiController
{
    /** @var \TotemV1Bundle\ApplicationServices\TurnoServices */
    private $turnoServices;

    /**
     * Confirmar turno
     *
     * @param Request $request Espera el resultado de una petición como parámetro
     * @return mixed
     * @Post("/turnos/confirmar")
     */
    public function confirmarTurno(Request $request)
    {
        $params = $request->request->all();
        $turnoServices = $this->getTurnoServices();
        return $turnoServices->recepcionar(
            $params,
            function ($response) {
                return $this->respuestaOk('Turno recepcionado con éxito', $response);
            },
            function ($err) {
                return $this->respuestaError($err);
            }
        );
    }
}
<?php


namespace ApiV1Bundle\Controller;

use ApiV1Bundle\ApplicationServices\FeriadoNacionalServices;
use FOS\RestBundle\Controller\Annotations\Get;
use FOS\RestBundle\Controller\Annotations\Post;
use FOS\RestBundle\Controller\Annotations\Delete;
use FOS\RestBundle\Controller\Annotations\Route;
use Symfony\Component\HttpFoundation\Request;

/**
 * Class FeriadoNacionalController
 * @package ApiV1Bundle\Controller
 */

class FeriadoNacionalController extends ApiController
{
    /** @var FeriadoNacionalServices */
    private $feriadoNacionalServices;

    /**
     * Crear Feriado Nacional
     *
     * @param Request $request Datos necesarios para ejecutar la petición
     * @return mixed
     * @Post("/feriadoNacional")
     */
    public function postAction(Request $request)
    {
        $params = $request->request->all();
        $this->feriadoNacionalServices = $this->getFeriadoNacionalServices();
        return $this->feriadoNacionalServices->create(
            $params,
            function ($tramite) {
                return $this->respuestaOk('Feriado Nacional agregado con éxito', [
                    'id' => $tramite->getId()
                ]);
            },
            function ($err) {
                return $this->respuestaError($err);
            }
        );
    }

    /**
     * Eliminar Feriado Nacional
     *
     * @param date $fecha Fecha a eliminar
     * @return mixed
     * @Delete("/feriadoNacional/{fecha}")
     */
    public function deleteAction($fecha)
    {
        $this->feriadoNacionalServices = $this->getFeriadoNacionalServices();

        return $this->feriadoNacionalServices->delete(
            $fecha,
            function () {
                return $this->respuestaOk('Feriado Nacional eliminado con éxito');
            },
            function ($err) {
                return $this->respuestaError($err);
            }
        );
    }

     /**
     * Retorna todos los feriados nacionales
      *
     * @return mixed
     * @Get("/feriadoNacional")
     */
    public function getFeriadoNacional()
    {
        $this->feriadoNacionalServices = $this->getFeriadoNacionalServices();
        return $this->feriadoNacionalServices->getAllFeriadoNacional();
    }
}

<?php

namespace TotemV1Bundle\Controller;

use FOS\RestBundle\Controller\Annotations\Get;
use TotemV1Bundle\Entity\Response\Respuesta;

/**
 * Class DefaultController
 * @package TotemV1Bundle\Controller
 *
 * @author Fausto Carrera <fcarrera@hexacta.com>
 */

class DefaultController extends ApiController
{

    /**
     * Controller por defecto
     *
     * @return Respuesta
     * @Get("/", name="totem_index")
     */
    public function indexAction()
    {
        return $this->respuestaData(null, []);
    }

    /**
     * Version de la API de Totem
     *
     * @return Respuesta
     * @Get("/version", name="totem_version")
     */
    public function versionAction()
    {
        return $this->respuestaData(null, [
            'API' => 'Turnos por demanda',
            'version' => $this->container->getParameter('totem_version'),
        ]);
    }
}

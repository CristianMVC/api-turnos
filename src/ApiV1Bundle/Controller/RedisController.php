<?php

namespace ApiV1Bundle\Controller;
use FOS\RestBundle\Controller\Annotations as Rest;
use FOS\RestBundle\Controller\Annotations\Get;
use FOS\RestBundle\Controller\Annotations\Put;
use FOS\RestBundle\Controller\Annotations\Post;
use FOS\RestBundle\Controller\Annotations\Route;
use Symfony\Component\HttpFoundation\Request;
use ApiV1Bundle\ApplicationServices\RolesServices;
use ApiV1Bundle\Entity\User;
use Nelmio\ApiDocBundle\Annotation\ApiDoc;

class RedisController extends ApiController
{
    
    
      /**
     * Limpia el cache en Redis de los objetos creados para api-turnos, Solo Admin
     * @ApiDoc()
     * @return mixed
     * @Post("/redis/flush")
     */
    public function flushAll(Request $request) {
        $authorization = $request->headers->get('Authorization', null);
        $roleService = $this->getRolesServices();
        $validateResultado = $roleService->getUsuario($authorization);
        if (!$validateResultado->hasError() && $validateResultado->getEntity()->getUser()->getRol() == User::ROL_ADMIN) {
            return $this->getRedisServices()->flushAllDisp();
        }
    }

}

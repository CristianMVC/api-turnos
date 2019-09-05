REST Bundle
===========

[Volver](../README.md)

__Last update:__ 23.07.2017

Usamos el [FOSRestBundle](https://symfony.com/doc/master/bundles/FOSRestBundle/index.html) para los servicios REST.

```
<?php

namespace ApiV1Bundle\Controller;

use FOS\RestBundle\Controller\Annotations\Get;
use FOS\RestBundle\Controller\Annotations\Post;
use FOS\RestBundle\Controller\Annotations\Put;
use FOS\RestBundle\Controller\Annotations\Delete;
use FOS\RestBundle\Controller\FOSRestController;
use FOS\RestBundle\Controller\Annotations\Route;
use Symfony\Component\HttpFoundation\JsonResponse;

class RestTestController extends FOSRestController
{
    /**
     * GET Route annotation.
     * @Get("/test")
     */
    public function testGetAction()
    {
        $data = [
            'method' => 'GET',
            'lorem' => 'ipsum',
            'dolor' => 'sit amet'
        ];
        return new JsonResponse($data);
    }

    /**
     * POST Route annotation.
     * @Post("/test")
     */
    public function testPostAction()
    {
        $data = [
            'method' => 'POST',
            'lorem' => 'ipsum',
            'dolor' => 'sit amet'
        ];
        return new JsonResponse($data);
    }

    /**
     * PUT Route annotation.
     * @Put("/test")
     */
    public function testPutAction()
    {
        $data = [
            'method' => 'PUT',
            'lorem' => 'ipsum',
            'dolor' => 'sit amet'
        ];
        return new JsonResponse($data);
    }

    /**
     * DELETE Route annotation.
     * @Delete("/test")
     */
    public function testDeleteAction()
    {
        $data = [
            'method' => 'DELETE',
            'lorem' => 'ipsum',
            'dolor' => 'sit amet'
        ];
        return new JsonResponse($data);
    }
}

```
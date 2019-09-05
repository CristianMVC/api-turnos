<?php
/**
 * Created by PhpStorm.
 * User: cristian
 * Date: 10/06/19
 * Time: 15:38
 */

namespace ApiV1Bundle\ApplicationServices;

use ApiV1Bundle\Entity\Tramite;
use Symfony\Component\DependencyInjection\Container;
use ApiV1Bundle\Entity\Response\RespuestaConEstado;
use ApiV1Bundle\Entity\Etiqueta;
use Doctrine\ORM\EntityManager;
use ApiV1Bundle\Entity\Response\Respuesta;
use ApiV1Bundle\Repository\EtiquetaRepository;
use ApiV1Bundle\Repository\TramiteRepository;

class EtiquetaServices extends SNTServices
{

 private $em;

/** @var EtiquetaRepository  */
 private $etiquetaRepository;
 /** @var TramiteRepository  */
 private $tramiteRepository;


    public function __construct( 
            Container $container, EntityManager $entityManager, 
            EtiquetaRepository $etiquetaRepository,
            TramiteRepository $tramiteRepository) {

        parent::__construct($container);
        $this->em = $entityManager;
        $this->etiquetaRepository = $etiquetaRepository;
        $this->tramiteRepository = $tramiteRepository;
        
    }



    public function crearEtiqueta($nombre) {
        $error = $this->verificarEtiqueta($nombre) ;

        if(!is_null(($error))) {
            return $this->respuestaError( $error);
        }

        $etiqueta = new Etiqueta();
        $etiqueta->setNombre($nombre[key($nombre)]);

        try {
            $this->em->persist($etiqueta);
            $this->em->flush();
            return $this->respuestaOk('Etiqueta creada');

        } catch (\Exception $e) {
            return $this->respuestaError( $e->getMessage());
        }

    }



    public function listarEtiquetas($limit, $offset) {

        try {
            $result = $this->etiquetaRepository->findAllPaginated($offset, $limit);



            $resultset = [
                'resultset' => [
                    'count' => $result['total'],
                    'offset' => $offset,
                    'limit' => $limit
                ]
            ];


            return $this->respuestaData($resultset,  $result['result']);

        } catch (\Exception $e) {
            return $this->respuestaError( $e->getMessage());
        }

    }


    public function eliminarEtiqueta($id) {
        try {

            if (!is_numeric($id)) {
                return $this->respuestaError('El identificador debe ser numérico');
            }

            $result = $this->em
                ->getRepository('ApiV1Bundle:Etiqueta')
                ->find($id);

            if (is_null($result)) {
                return $this->respuestaError('Etiqueta "' . $id . '" no existe');

            } else {

                $this->em->remove($result);
                $this->em->flush();
                return $this->respuestaOk('Etiqueta eliminada');
            }

        } catch (\Exception $e) {
            return $this->respuestaError( $e->getMessage());
        }


    }

    private function verificarEtiqueta($nombre) {
        $error = null;

        if( is_null(key($nombre) )) {
            $error[]  = ' Debe ingresar el nombre de la etiqueta ';
        } else {
            if( strtolower(key($nombre )) != 'nombre' ) {
                $error[] = 'El parámetro "' .key($nombre).'" es incorrecto';
            } else {
                if(empty($nombre[key($nombre)])) {
                    $error[] = 'El nombre no debe ser nulo';

                }
            }
        }

        return $error;
    }



    public function  modificarEtiqueta($id, $nombre) {
        $error = $this->verificarEtiqueta($nombre) ;

        if(!is_null(($error))) {
            return $this->respuestaError( $error);
        }

        try {
            $etiqueta = $this->em->getRepository('ApiV1Bundle:Etiqueta')->find($id);

            if(is_null( $etiqueta)) {
                return $this->respuestaError('La etiqueta indicada no existe');
            }

            $etiqueta->setNombre($nombre['nombre']);
            $this->em->persist($etiqueta);
            $this->em->flush();

            return $this->respuestaOk('Etiqueta modificada');
        } catch(\Exception $e) {

            return $this->respuestaError( $e->getMessage());
        }


    }


    public function asignarEtiqueta($params, $idTramite)
    {
        $error= null;


        $tramite = $this->em->getRepository('ApiV1Bundle:Tramite')->find($idTramite);

        if (is_null($tramite)) {
            return $this->respuestaError('Trámite inexistente');
        }


        if (!array_key_exists("etiquetas", $params)) {

            return $this->respuestaError('Debe ingresar al menos una etiqueta');
        }



        if (count($params['etiquetas']) < 1) {

            $tramite->addEtiquetas([]);
            $this->em->persist($tramite);
            $this->em->flush();
            return $this->respuestaOk('Tramite sin etiquetas');
        }



        foreach ($params['etiquetas'] as $etiqueta) {
            if(is_null($this->em->getRepository('ApiV1Bundle:Etiqueta')->findOneByNombre($etiqueta))) {
                $error[] = 'Etiqueta '.$etiqueta.' no existe';
            } else {
                $etiquetas[] = $this->em->getRepository('ApiV1Bundle:Etiqueta')->findOneByNombre($etiqueta);
            }
        }

         if(count($error) > 0) {
             return $this->respuestaError($error);
         }
            try {
                $tramite->addEtiquetas($etiquetas);
                $this->em->persist($tramite);
                $this->em->flush();

                return $this->respuestaOk('Se añadieron las etiquetas');

            } catch (\Exception $e) {

                return $this->respuestaError($e->getMessage());
            }

        }


        public function filtrarPorEtiqueta($params, $offset, $limit) {


            if(is_null($params)) {
               return $this->respuestaError("Debe ingresar al menos una etiqueta");
              }
            
            $result = $this->etiquetaRepository->filtrarPorEtiqueta($params, $offset, $limit);
            
             $resultset = [
                'resultset' => [
                'count' => count($result),
                'offset' => $offset,
                'limit' => $limit
            ]
        ];

        return $this->respuestaData($resultset, $result);


        }



    public function  listarEtiquetasPorTramite($idTramite, $offset, $limit) {

        $result = $this->etiquetaRepository->etiquetasPorTramite($idTramite, $offset, $limit);

        $resultset = [
            'resultset' => [
                'count' => count($result),
                'offset' => $offset,
                'limit' => $limit
            ]
        ];

        return $this->respuestaData($resultset,  $result);

   }




    protected function respuestaData($metadata, $result)
    {
        return new Respuesta($metadata, $result);
    }

    protected function respuestaOk($message, $additional = '') {
        return new RespuestaConEstado(
            RespuestaConEstado::STATUS_SUCCESS,
            RespuestaConEstado::CODE_SUCCESS,
            $message,
            '',
            $additional
        );
    }

    protected function respuestaError($message) {

        return new RespuestaConEstado(
            RespuestaConEstado::STATUS_FATAL,
            RespuestaConEstado::CODE_FATAL,
            $message,
            '',
            ''
        );
    }


}
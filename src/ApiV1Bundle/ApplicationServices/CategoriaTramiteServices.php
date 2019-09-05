<?php
/**
 * Created by PhpStorm.
 * User: cristian
 * Date: 31/05/19
 * Time: 12:10
 */

namespace ApiV1Bundle\ApplicationServices;
use Symfony\Component\DependencyInjection\Container;
use ApiV1Bundle\Entity\Response\RespuestaConEstado;
use ApiV1Bundle\Entity\CategoriaTramite;
use Doctrine\ORM\EntityManager;
use ApiV1Bundle\Entity\Response\Respuesta;
use ApiV1Bundle\Repository\CategoriaTramiteRepository;

class CategoriaTramiteServices  extends SNTServices
{
  private $em;
    /** @var CategoriaTramiteRepository  */
  private $categoriaTramiteRepository;


    public function __construct( Container $container, EntityManager $entityManager, CategoriaTramiteRepository $categoriaTramiteRepository) {

        parent::__construct($container);
        $this->em = $entityManager;
        $this->categoriaTramiteRepository = $categoriaTramiteRepository;
    }

    public function crearCategoria($nombre) {
      $error = $this->verificarCategoria($nombre) ;

     if(!is_null(($error))) {
         return $this->respuestaError( $error);
      }

          $categoriaTramite = new CategoriaTramite();
          $categoriaTramite->setNombre($nombre[key($nombre)]);
       try {
              $this->em->persist($categoriaTramite);
              $this->em->flush();
              return $this->respuestaOk('Categoría creada');

             } catch (\Exception $e) {

                  return $this->respuestaError( $e->getMessage());
             }

    }



    public function listarCategorias($limit, $offset) {

       try {

           $result =$this->em->getRepository('ApiV1Bundle:CategoriaTramite')
                                         ->findBy(array(), null, $limit, $offset);



           $resultset = [
               'resultset' => [
                   'count' => count($this->em->getRepository('ApiV1Bundle:CategoriaTramite')->findAll()),
                   'offset' => $offset,
                   'limit' => $limit
               ]
           ];


           return $this->respuestaData($resultset, $result);

       } catch (\Exception $e) {
           return $this->respuestaError( $e->getMessage());
       }

    }


    public function eliminarCategoria($id) {
        try {

            if (!is_numeric($id)) {
                return $this->respuestaError('El identificador debe ser numérico');
            }

            $result = $this->em
                ->getRepository('ApiV1Bundle:CategoriaTramite')
                ->find($id);

            if (is_null($result)) {
                return $this->respuestaError('Categoría "' . $id . '" no existe');

            } else {

              $this->em->remove($result);
              $this->em->flush();
              return $this->respuestaOk('Categoría eliminada');
            }

        } catch (\Exception $e) {
            return $this->respuestaError( $e->getMessage());
        }


    }


    public function modificarCategoria($id, $nombre) {
        $error = $this->verificarCategoria($nombre) ;

        if(!is_null(($error))) {
            return $this->respuestaError( $error);
        }

        try {
            $categoria = $this->em->getRepository('ApiV1Bundle:CategoriaTramite')->find($id);

            if(is_null( $categoria)) {
                return $this->respuestaError('La categoría indicada no existe');
            }

            $categoria->setNombre($nombre['categoria']);
            $this->em->persist( $categoria);
            $this->em->flush();

            return $this->respuestaOk('Categoría modificada');
        } catch(\Exception $e) {

            return $this->respuestaError( $e->getMessage());
        }

    }


    public function asignarCategoria($idCat,  $idTram) {

        $tramite = $this->em->getRepository('ApiV1Bundle:Tramite')->find($idTram);

        if(is_null( $tramite)) {
            return $this->respuestaError('Tramite inexistente');
        }

        $categoria = $this->em->getRepository('ApiV1Bundle:CategoriaTramite')->find($idCat);

        if(is_null( $tramite)) {
            return $this->respuestaError('Categoría inexistente');
        }

        $tramite-> setCategoriaTramite($categoria);

        try {
               $this->em->persist( $tramite);
               $this->em->flush();
               return $this->respuestaOk('Categoría asignada');

              } catch (\Exception $e) {

               return $this->respuestaError( $e->getMessage());
              }


    }
    
    public function filtrarPorCategoria($params, $limit, $offset) {
        
        if(is_null($params)) {
           return $this->respuestaError("Debe ingresar una categoria");
        }

         $result = $this->em->getRepository('ApiV1Bundle:CategoriaTramite')
                        ->findOneBy(array('nombre' => $params), null, $limit, $offset);
         
         
         if(is_null($result)) {
            return $this->respuestaError('Categoria inexistente'); 
         }
         
          $resultset = [
               'resultset' => [
                   'count' => count($result->getTramite()),
                   'offset' => $offset,
                   'limit' => $limit
               ]
           ];


           return $this->respuestaData($resultset, $result->getTramite());
    }



    private function verificarCategoria($categoria) {
        $error = null;

        if( is_null(key($categoria) )) {
            $error[]  = ' Debe ingresar una categoría ';
        } else {
            if( strtolower(key($categoria )) != 'categoria' ) {
                $error[] = 'El parametro "' .key($categoria).'" es incorrecto';
            } else {
                if(empty($categoria[key($categoria)])) {
                    $error[] = 'La categoría está vacia';

                }
            }
        }

        return $error;
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
<?php
/**
 * Created by PhpStorm.
 * User: cristian
 * Date: 26/08/19
 * Time: 10:21
 */

namespace ApiV1Bundle\ApplicationServices;


use ApiV1Bundle\Repository\MiArgentinaRepository;
use ApiV1Bundle\Repository\TurnoRepository;
use Symfony\Component\DependencyInjection\Container;
use Doctrine\ORM\EntityManager;
use ApiV1Bundle\Entity\Response\RespuestaConEstado;
use ApiV1Bundle\Entity\Validator\MiargentinaValidator;
use ApiV1Bundle\Helper\ServicesHelper;

class MiArgentinaServices  extends SNTServices
{

    private $repository;
    private $em;
    
    /** @var MiargentinaValidator  */
    private $validator;

    public function __construct(Container $container, EntityManager $em, MiArgentinaRepository $repository, MiargentinaValidator $validator, TurnoRepository $turnoRepository) {

        parent::__construct($container);

        $this->repository = $repository;
        $this->em = $em;
        $this->validator = $validator;
        $this->turnoRepository = $turnoRepository;
    }


    public function tramitesMiArgentina($limit, $offset) {

        $result = $this->repository->getTotalMiArgentina($limit, $offset);

        $resultset = [
            'resultset' => [
                'count' => count($result),
                'offset' => $offset,
                'limit' => $limit
            ]
        ];

        return $this->respuestaData($resultset, $result);
    }


    /**
     * Listar trámites por Area
     *
     * @param integer $organismoId Identificador único de organismo
     * @param integer $areaId Identificador único de área
     * @param integer $limit Cantidad máxima de registros a retornar
     * @param integer $offset Cantidad de registros a saltar
     * @return mixed
     */
    public function findTramitesPaginate($organismoId, $areaId, $limit, $offset)
    {

        if(is_null($areaId)) {

            $result = $this->repository->findTramitesOrganismoMiArgentina($organismoId, $limit, $offset);

            $resultset = [
                'resultset' => [
                    'count' =>count($result),
                    'offset' => $offset,
                    'limit' => $limit
                ]
            ];

        } else {

            $result = $this->repository->findTramitesPaginateMiArgentina($areaId, $limit, $offset);
            $resultset = [
                'resultset' => [
                    'count' => count($result),
                    'offset' => $offset,
                    'limit' => $limit
                ]
            ];

        }


        return $this->respuestaData($resultset, $result);
    }



    /**
     * Obtener un trámite
     *
     * @param integer $id Identificador único del trámite del que se quiere obtener información
     * @return mixed
     */
    public function get($id) {

        $result = [];
        $tramite =  $this->em->getRepository('ApiV1Bundle:Tramite')->find($id);

        if (!is_null($tramite) && $tramite->getMiArgentina() && $tramite->getCategorias()) {
            $multi_cantidad = 0;
            $nombre = null;
            $multi = 0;
            $area = $tramite->getArea();
            $categorias = $tramite->getCategoriaTramite();
            $organismo =  ($area && isset($area[0]))?$area[0]->getOrganismo()->getNombre():"";
            $idCat = ($categorias && isset($categorias))?$categorias->getId():"";

            foreach( $tramite->getArea() as $a ) {
                $nombre[] = $a->getNombre();
            }

            if(!is_null($tramite->getPuntosAtencion())) {
                foreach ($tramite->getPuntosAtencion() as $pt) {

                    if($pt->getMultiturno() == 1) {
                        $multi = 1;
                        $multi_cantidad = $pt->getMultiturnoCantidad() ;
                    }
                }
            }

            $result = [
                'id' => $tramite->getId(),
                'argentinaGobArId' => $tramite->getIdArgentinaGobAr(),
                'nombre' => $tramite->getNombre(),
                'duracion' => $tramite->getDuracion(),
                'descripcion' => $tramite->getDescripcion(),
                'requisitos' => $tramite->getRequisitos(),
                'campos' => $tramite->getFormulario()->getCampos(),
                'visibilidad' => $tramite->getVisibilidad(),
                'excepcional' => $tramite->getExcepcional(),
                'area' => $nombre,
                'organismo' => $organismo,
                'idCat' =>  $idCat,
                'miArgentina' =>  $tramite->getmiArgentina(),
                'multiturno' => $multi,
                'multiturno_cantidad' => $multi_cantidad
            ];
        }


     //   $result = $this->repository->findTramiteMiArgentina($id);

        $resultset = [
            'resultset' => [
                'count' =>count($result),
                'offset' => 0,
                'limit' => 1
            ]
        ];

        return $this->respuestaData($resultset, $result);
    }


    /**
     * Obtener un trámite
     *
     * @param integer $parametros requeridos
     * @param integer $limit Cantidad máxima de registros a retornar
     * @param integer $offset Cantidad de registros a saltar
     * @return mixed
     */

    public function findFiltrarPorCategoriaMiArgentina($categoria, $limit, $offset) {

        if(is_null($categoria)) {

            return $this->respuestaError("Debe ingresar una categoria");
        }
        $result = $this->repository->findFiltrarPorCategoriaMiArgentina($categoria, $limit, $offset);


        if(is_null($result)) {
            return $this->respuestaError('Categoria inexistente');
        }

        $resultset = [
            'resultset' => [
                'count' => count($result),
                'offset' => $offset,
                'limit' => $limit
            ]
        ];

        return $this->respuestaData($resultset, $result);

    }


    public function filtrarPorEtiquetaMiArgentina($params, $offset, $limit) {

        if(is_null($params)) {
            return $this->respuestaError("Debe ingresar al menos una etiqueta");
        }

        $result = $this->repository->filtrarPorEtiquetaMiArgentina($params, $offset, $limit);

        $resultset = [
            'resultset' => [
                'count' => count($result),
                'offset' => $offset,
                'limit' => $limit
            ]
        ];

        return $this->respuestaData($resultset, $result);

    }


    public function listarCategorias($limit, $offset) {

        $result = $this->repository->listarCategoriasMiArgentina($limit, $offset);

            $resultset = [
                'resultset' => [
                    'count' => count($result),
                    'offset' => $offset,
                    'limit' => $limit
                ]
            ];

            return $this->respuestaData($resultset, $result);

    }


    public function findAllSolicitante ($desde, $hasta, $cuil) {

        $result = $this->repository->findTurnosPorSolicitanteMiArgentina( $desde, $hasta, $cuil);

        $resultset = [
            'resultset' => [
                'count' => count($result),
                'desde' => $desde,
                'hasta' => $hasta
            ]
        ];

        return $this->respuestaData($resultset, $result);

    }


    public function filtrarPorEtiquetaBuscar($params, $offset, $limit) {
        if (is_null($params) || !is_array($params)) {
            return $this->respuestaError("Debe ingresar al menos una etiqueta");
        }
        $result = $this->repository->findAllTramiteByEtiquetaPaginateMiArgentina($params, $offset, $limit);
        $resultset = [
            'resultset' => [
                'count' => count($result),
                'offset' => $offset,
                'limit' => $limit
            ]
        ];
        return $this->respuestaData($resultset, $result);
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


    public function puedeSacarTurnoAction ($params, $cuil) {
        
        $errors = $this->validator->validar($params, [
            'idPuntoAtención' => 'required:integer',
            'cuil'  => 'required:cuil',
            'idTramite' => 'required:integer',
            'fecha' => 'required:date'
        ]);
        if(count($errors)) {
            return $this->respuestaError($errors);
        }
        if (! count($errors)) {
            $verificacionTurno = $this->turnoRepository->findTurnosByPuntoTramiteCuil(
                $params["idPuntoAtención"],
                $params["idTramite"],
                $params["fecha"],
                ServicesHelper::buildValidDocument($cuil)
            );
            if(count($verificacionTurno)){ 
            //tiene turnos duplicados
                if(!$this->validator->validarTurnosPorCuil($params, $params["idPuntoAtención"], $params["idTramite"]) ){
                    $errors[] = 'Ya existe un turno para el ciudadano para esa fecha y tramite';
                }
            }
        }
        if(count($errors)) {
            return $this->respuestaError($errors);
        }
        $resultset = [
            'resultset' => [
                'count' => 1
            ]
        ];

        return $this->respuestaData($resultset, ["permite"=>"1"]);

    }


}
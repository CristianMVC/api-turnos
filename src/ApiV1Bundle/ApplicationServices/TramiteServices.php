<?php

namespace ApiV1Bundle\ApplicationServices;

use SNT\Domain\Services\Parser;
use ApiV1Bundle\Entity\Factory\FormularioFactory;
use ApiV1Bundle\Entity\Factory\TramiteFactory;
use ApiV1Bundle\Entity\Sync\FormularioSync;
use ApiV1Bundle\Entity\Validator\FormularioValidator;
use Symfony\Component\DependencyInjection\Container;
use ApiV1Bundle\Entity\Sync\TramiteSync;
use ApiV1Bundle\Entity\Validator\TramiteValidator;
use ApiV1Bundle\Helper\ServicesHelper;
use ApiV1Bundle\Repository\TramiteRepository;
use ApiV1Bundle\Repository\AreaRepository;
use ApiV1Bundle\Entity\CamposGenericos;
use ApiV1Bundle\Repository\TurnoRepository;
use ApiV1Bundle\Repository\PuntoTramiteRepository;

/**
 * Class TramiteServices
 * @package ApiV1Bundle\ApplicationServices
 */

class TramiteServices extends SNTServices
{
    /** @var TramiteRepository  */
    private $tramiteRepository;
    /** @var AreaRepository  */
    private $areaRepository;
    /** @var TramiteValidator  */
    private $tramiteValidator;
    /** @var FormularioValidator  */
    private $formularioValidator;
    /** @var TurnoRepository  */
    private $turnoRepository;
    /** @var PuntoTramiteRepository  */
    private $puntoTramiteRepository;
    

    /**
     * TramiteServices constructor.
     * @param Container $container
     * @param TramiteRepository $tramiteRepository
     * @param AreaRepository $areaRepository
     * @param TramiteValidator $tramiteValidator
     * @param FormularioValidator $formularioValidator
     * @param TurnoRepository $turnoRepository
     * @param Parser $parser
     * @param PuntoTramiteRepository $puntoTramiteRepository
     */
    public function __construct(
        Container $container,
        TramiteRepository $tramiteRepository,
        AreaRepository $areaRepository,
        TramiteValidator $tramiteValidator,
        FormularioValidator $formularioValidator,
        TurnoRepository $turnoRepository,
        Parser $parser,
        PuntoTramiteRepository $puntoTramiteRepository
    ) {
        parent::__construct($container);
        $this->tramiteRepository = $tramiteRepository;
        $this->areaRepository = $areaRepository;
        $this->tramiteValidator = $tramiteValidator;
        $this->formularioValidator = $formularioValidator;
        $this->turnoRepository = $turnoRepository;
        $this->parserService = $parser;
        $this->puntoTramiteRepository = $puntoTramiteRepository;
    }

    /**
     * Encontrar resultados y paginar
     *
     * @param string $query String con la consulta de trámites
     * @param integer $limit Cantidad máxima de registros a retornar
     * @param integer $offset Cantidad de registros a saltar
     * @return mixed
     */
    public function findAllPaginate($query, $limit, $offset)
    {
        $result = $this->tramiteRepository->findAllTramitePaginate($query, $limit, $offset);

        $resultset = [
            'resultset' => [
                'count' => $this->tramiteRepository->getTotal($query),
                'offset' => $offset,
                'limit' => $limit
            ]
        ];

        return $this->respuestaData($resultset, $result);
    }



    public function findAreasTramite($id) {


        $result = $this->tramiteRepository->findAreasTramite($id);
        $resultset = [
            'resultset' => [
                'offset' => 0,
                'limit' => 0
            ]
        ];

        return $this->respuestaData( $resultset, $result);


    }




    /**
     * Obtener un trámite
     *
     * @param integer $id Identificador único del trámite del que se quiere obtener información
     * @param callback $success Callback para devolver respuesta exitosa
     * @param callback $error Callback para devolver respuesta fallida
     * @return mixed
     */
    public function get($id, $success, $error)
    {

        $result = [];
        $tramite = $this->tramiteRepository->find($id);
        $validateResultado = $this->tramiteValidator->validarTramite($tramite);


        if (!$validateResultado->hasError()) {
            
            $area = $tramite->getArea();
            $categorias = $tramite->getCategoriaTramite();
            $organismo =  ($area && isset($area[0]))?$area[0]->getOrganismo()->getNombre():"";
            $idCat = ($categorias && isset($categorias))?$categorias->getId():"";
            $result = [
                'id' => $tramite->getId(),
                'argentinaGobArId' => $tramite->getIdArgentinaGobAr(),
                'nombre' => $tramite->getNombre(),
                'duracion' => $tramite->getDuracion(),
                'descripcion' => $tramite->getDescripcion(),
                'requisitos' => ServicesHelper::parseRequisitos($tramite->getRequisitos()),
                'campos' => $this->getCamposFormulario($tramite),
                'visibilidad' => $tramite->getVisibilidad(),
                'excepcional' => $tramite->getExcepcional(),
                'area' => (!$tramite->getOrg()) ? $tramite->getArea()[0]->getNombre() : "",
                'organismo' => $organismo,
                'idCat' =>  $idCat,
                'miArgentina' =>  $tramite->getmiArgentina(),
            ];
        }


        return $this->processError(
            $validateResultado,
            function () use ($success, $result) {
                return call_user_func($success, $result);
            },
            $error
        );
    }

    /**
     * Obtener los campos del formulario del tramite
     *
     * @param integer $id identificador único del trámite
     * @param callback $success Callback para devolver respuesta exitosa
     * @param callback $error Callback para devolver respuesta fallida
     * @return mixed
     */
    public function getCampos($id, $success, $error)
    {
        $result = [];
        $tramite = $this->tramiteRepository->find($id);
        $validateResultado = $this->tramiteValidator->validarTramite($tramite);

        if (!$validateResultado->hasError()) {
            $result = [
                'id' => $tramite->getId(),
                'campos' => $this->getCamposFormulario($tramite)
            ];
        }

        return $this->processError(
            $validateResultado,
            function () use ($success, $result) {
                return call_user_func($success, $result);
            },
            $error
        );
    }

    /**
     * Obtener los requisitos del tramite
     *
     * @param integer $id identificador único del trámite
     * @param callback $success Callback para devolver respuesta exitosa
     * @param callback $error Callback para devolver respuesta fallida
     * @return mixed
     */
    public function getRequisitos($id, $success, $error)
    {
        $result = [];
        $tramite = $this->tramiteRepository->find($id);
        $validateResultado = $this->tramiteValidator->validarTramite($tramite);

        if (!$validateResultado->hasError()) {
            $result = [
                'id' => $tramite->getId(),
                'requisitos' => $this->parserService->render($tramite->getRequisitos())

            ];
        }

        return $this->processError(
            $validateResultado,
            function () use ($success, $result) {
                return call_user_func($success, $result);
            },
            $error
        );
    }

    /**
     * Obtener el horizonte del tramite
     *
     * @param integer $id identificador único del trámite
     * @param callback $success Callback para devolver respuesta exitosa
     * @param callback $error Callback para devolver respuesta fallida
     * @return mixed
     */
    public function getHorizonte($id, $success, $error)
    {
        $tramite = $this->tramiteRepository->findHorizonte($id);
        $validateResultado = $this->tramiteValidator->validarTramite($tramite);

        return $this->processError(
            $validateResultado,
            function () use ($success, $tramite) {
                return call_user_func($success, $tramite);
            },
            $error
        );
    }

    /**
     * Crea un trámite
     *
     * @param array $params Array con los datos del trámite a crear
     * @param callback $sucess Callback para devolver respuesta exitosa
     * @param callback $error Callback para devolver respuesta fallida
     * @return mixed
     */
    public function create($params, $sucess, $error)
    {
        $params['requisitos'] = isset($params['requisitos']) ? ServicesHelper::mergeRequisitos($params['requisitos']) : '';

        $tramiteFactory = new TramiteFactory(
            $this->areaRepository,
            new FormularioFactory($this->formularioValidator),
            $this->tramiteValidator
        );

        $validateResult = $tramiteFactory->create($params);



        return $this->processResult(
            $validateResult,
            function ($entity) use ($sucess) {
                return call_user_func($sucess, $this->tramiteRepository->save($entity));
            },
            $error
        );
    }

    /**
     * Edita un trámite
     *
     * @param array $params Array con los datos del trámite a crear
     * @param integer $id identificador único del trámite
     * @param callback $sucess Callback para devolver respuesta exitosa
     * @param callback $error Callback para devolver respuesta fallida
     * @return mixed
     */
    public function edit($params, $id, $sucess, $error)
    {
        $params['requisitos'] = isset($params['requisitos']) ? ServicesHelper::mergeRequisitos($params['requisitos']) : '';

        $tramiteSync = new TramiteSync(
            $this->areaRepository,
            $this->tramiteRepository,
            new FormularioFactory($this->formularioValidator),
            $this->tramiteValidator,
            new FormularioSync($this->formularioValidator),
            $this->turnoRepository
        );

        $validateResult = $tramiteSync->edit($id, $params);

        return $this->processResult(
            $validateResult,
            function () use ($sucess) {
                return call_user_func($sucess, $this->tramiteRepository->flush());
            },
            $error
        );
    }

    /**
     * Borra un trámite
     *
     * @param integer $id identificador único del trámite
     * @param callback $sucess Callback para devolver respuesta exitosa
     * @param callback $error Callback para devolver respuesta fallida
     * @return mixed
     */
    public function delete($id, $idArea, $sucess, $error)
    {

        $tramiteSync = new TramiteSync(
            $this->areaRepository,
            $this->tramiteRepository,
            new FormularioFactory($this->formularioValidator),
            $this->tramiteValidator,
            new FormularioSync($this->formularioValidator),
            $this->turnoRepository
        );
        $validateResult = $tramiteSync->delete($id, $idArea);

        if($idArea) {

            return $this->processResult(
                $validateResult,
                function () use ($sucess) {
                    return call_user_func($sucess, $this->tramiteRepository->flush());
                },
                $error
            );

        } else {

            return $this->processResult(
                $validateResult,
                function ($entity) use ($sucess) {
                    return call_user_func($sucess, $this->tramiteRepository->remove($entity));
                },
                $error
            );
        }
    }

    /**
     * Obtener las provincias donde se puede realizar el trámite
     *
     * @param integer $tramiteId identificador único del trámite
     * @param integer $limit Cantidad máxima de registros a retornar
     * @param integer $offset Cantidad de registros a saltar
     * @return mixed
     */
    public function findProvinciasPaginate($offset, $limit, $tramiteId, $pdaId = false)
    {
        $result = $this->tramiteRepository->findProvinciasPaginate($limit, $offset, $tramiteId, $pdaId);
        $resultset = [
            'resultset' => [
                'count' => $this->tramiteRepository->getTotalProvincias($tramiteId),
                'offset' => $offset,
                'limit' => $limit
            ]
        ];
        return $this->respuestaData($resultset, $result);
    }

    /**
     * Obtener las localidades donde se puede realizar el trámite
     *
     * @param integer $limit Cantidad máxima de registros a retornar
     * @param integer $offset Cantidad de registros a saltar
     * @param integer $tramiteId identificador único del trámite
     * @param integer $provinciaId identificador único de la provincia
     * @return mixed
     */
    public function findLocalidadesPaginate($offset, $limit, $tramiteId, $provinciaId)
    {
        $result = $this->tramiteRepository->findLocalidadesPaginate($limit, $offset, $tramiteId, $provinciaId);
        $resultset = [
            'resultset' => [
                'count' => $this->tramiteRepository->getTotalLocalidades($tramiteId, $provinciaId),
                'offset' => $offset,
                'limit' => $limit
            ]
        ];
        return $this->respuestaData($resultset, $result);
    }

    /**
     * Obtener los campos genericos del formulario
     */
    public function getCamposGenericos()
    {
        return $this->respuestaData([], CamposGenericos::getCamposGenericos());
    }

    /**
     * Obtenemos los campos del tramite
     *
     * @param object $tramite objeto trámite
     * @return mixed
     */
    private function getCamposFormulario($tramite)
    {
        $campos = $tramite->getFormulario()->getCampos();
        if ($tramite->getExcepcional()) {
            foreach ($campos as $key => $value) {
                if ($value['key'] == 'cuil') {
                    $campos[$key]['formComponent']['typeValue'] = 'text';
                    $campos[$key]['label'] = 'Documento extranjero';
                }
            }
        }
        return $campos;
    }
    
        /**
     * Obtener un trámite dado su ID y el punto de atención
     *
     * @param integer $tramiteId Identificador único del trámite del que se quiere obtener información
     * @param integer $puntoAtencionId Identificador único del punto de atención
     * @return mixed
     */
    public function getPDATramiteItemAction($puntoAtencionId, $tramiteId)
    {
        $tramite = $this->tramiteRepository->find($tramiteId);
        if ($tramite) {
            $grupoTramite = $this->tramiteRepository->getGrupotramiteIdByPunto($puntoAtencionId, $tramiteId);
            $puntoTramite = $this->puntoTramiteRepository->findOneBy([
                'puntoAtencionId' => $puntoAtencionId,
                'tramite' => $tramite
            ]);
            
            return $this->respuestaData([], [
                'id' => $tramite->getId(),
                'argentinaGobArId' => $tramite->getIdArgentinaGobAr(),
                'nombre' => $tramite->getNombre(),
                'duracion' => $tramite->getDuracion(),
                'requisitos' => $this->parserService->render($tramite->getRequisitos()),
                'visibilidad' => $tramite->getVisibilidad(),
                'grupoTramiteId' => $grupoTramite['grupoTramiteId'],
                'permite_otro_cantidad' => ($puntoTramite)?$puntoTramite->getPermiteOtroCantidad():"",
                'permite_otro' => ($puntoTramite)?$puntoTramite->getPermiteOtro():"",
                'multiturno_cantidad' => ($puntoTramite)?$puntoTramite->getMultiturnoCantidad():"",
                'multiturno' => ($puntoTramite)?$puntoTramite->getMultiturno():""
                
                
            ]);
        }
        return $this->respuestaData([], null);
    }





}

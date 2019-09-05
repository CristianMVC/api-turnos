<?php

namespace ApiV1Bundle\ApplicationServices;

use ApiV1Bundle\Entity\PuntoTramite;
use ApiV1Bundle\Entity\Response\Respuesta;
use ApiV1Bundle\Entity\Sync\DiaNoLaborableSync;
use ApiV1Bundle\Entity\Sync\PuntoAtencionSync;
use ApiV1Bundle\Entity\Tramite;
use ApiV1Bundle\Entity\Turno;
use ApiV1Bundle\Entity\ValidateResultado;
use ApiV1Bundle\ExternalServices\PuntoAtencionIntegration;
use ApiV1Bundle\Repository\DiaNoLaborableRepository;
use ApiV1Bundle\Repository\FeriadoNacionalRepository;
use ApiV1Bundle\Repository\PuntoTramiteRepository;
use Symfony\Component\DependencyInjection\Container;
use ApiV1Bundle\Entity\Factory\PuntoAtencionFactory;
use ApiV1Bundle\Entity\Validator\PuntoAtencionValidator;
use ApiV1Bundle\Repository\PuntoAtencionRepository;
use ApiV1Bundle\Repository\TramiteRepository;
use ApiV1Bundle\Repository\LocalidadRepository;
use ApiV1Bundle\Repository\ProvinciaRepository;
use ApiV1Bundle\Repository\AreaRepository;
use ApiV1Bundle\ApplicationServices\RedisServices;

/**
 * Class PuntoAtencionServices
 * @package ApiV1Bundle\ApplicationServices
 */
class PuntoAtencionServices extends SNTServices
{
    /** @var PuntoAtencionRepository  */
    private $puntoAtencionRepository;
    /** @var TramiteRepository  */
    private $tramiteRepository;
    /** @var LocalidadRepository  */
    private $localidadRepository;
    /** @var ProvinciaRepository  */
    private $provinciaRepository;
    /** @var AreaRepository  */
    private $areaRepository;
    /** @var PuntoAtencionValidator  */
    private $puntoAtencionValidator;
    /** @var DiaNoLaborableRepository  */
    private $diaNoLaborableRepository;
    /** @var PuntoAtencionIntegration  */
    private $puntoAtencionIntegration;
    /** @var FeriadoNacionalRepository  */
    private $feriadoNacionalRepository;
    /** @var RolesServices  */
    private $rolesServices;
    /** @var PuntoTramiteRepository  */
    private $puntoTramiteRepository;
    /** @var RedisServices  */
    private $redisServices;
    
    /**
     * PuntoAtencionServices constructor.
     * @param Container $container
     * @param PuntoAtencionRepository $puntoAtencionRepository
     * @param TramiteRepository $tramiteRepository
     * @param LocalidadRepository $localidadRepository
     * @param ProvinciaRepository $provinciaRepository
     * @param AreaRepository $areaRepository
     * @param PuntoAtencionValidator $puntoAtencionValidator
     * @param DiaNoLaborableRepository $diaNoLaborableRepository
     * @param PuntoAtencionIntegration $puntoAtencionIntegration
     * @param FeriadoNacionalRepository $feriadoNacionalRepository
     * @param RolesServices $rolesServices
     * @param PuntoTramiteRepository $puntoTramiteRepository
     * @param RedisServices $redisServices
     */
    public function __construct(
        Container $container,
        PuntoAtencionRepository $puntoAtencionRepository,
        TramiteRepository $tramiteRepository,
        LocalidadRepository $localidadRepository,
        ProvinciaRepository $provinciaRepository,
        AreaRepository $areaRepository,
        PuntoAtencionValidator $puntoAtencionValidator,
        DiaNoLaborableRepository $diaNoLaborableRepository,
        PuntoAtencionIntegration $puntoAtencionIntegration,
        FeriadoNacionalRepository $feriadoNacionalRepository,
        RolesServices $rolesServices,
        PuntoTramiteRepository $puntoTramiteRepository,
        RedisServices $redisServices
    ) {
        parent::__construct($container);
        $this->puntoAtencionRepository = $puntoAtencionRepository;
        $this->tramiteRepository = $tramiteRepository;
        $this->localidadRepository = $localidadRepository;
        $this->provinciaRepository  = $provinciaRepository;
        $this->areaRepository = $areaRepository;
        $this->puntoAtencionValidator = $puntoAtencionValidator;
        $this->diaNoLaborableRepository = $diaNoLaborableRepository;
        $this->puntoAtencionIntegration = $puntoAtencionIntegration;
        $this->feriadoNacionalRepository = $feriadoNacionalRepository;
        $this->rolesServices = $rolesServices;
        $this->puntoTramiteRepository = $puntoTramiteRepository;
        $this->redisServices = $redisServices;
    }

    /**
     * Listar puntos de atencion
     *
     * @param integer $limit Cantidad máxima de registros a retornar
     * @param integer $offset Cantidad de registros a saltar
     * @param string $authorization token del usuario logueado
     * @param callback $onError Callback para devolver respuesta fallida
     * @return mixed
     */
    public function findAllPaginate($offset, $limit, $authorization, $onError)
    {
        $validateResultado = $this->rolesServices->getUsuario($authorization);
        $result = [];
        $resultset = [];

        if (! $validateResultado->hasError()) {
            $result = $this->puntoAtencionRepository->findAllPaginate($offset, $limit, $validateResultado->getEntity());
            $resultset = [
                'resultset' => [
                    'count' => $this->puntoAtencionRepository->getTotal($validateResultado->getEntity()),
                    'offset' => $offset,
                    'limit' => $limit
                ]
            ];
        }

        return $this->processError(
            $validateResultado,
            function () use ($result, $resultset) {
                return $this->respuestaData($resultset, $result);
            },
            $onError
        );
    }

    /**
     * Listado de tramites por punto de atención
     *
     * @param integer $id Identificador único del punto de atención
     * @param integer $limit Cantidad máxima de registros a retornar
     * @param integer $offset Cantidad de registros a saltar
     * @param callback $onError Callback para devolver respuesta fallida
     * @return mixed
     */
    public function findTramitesPaginate($id, $offset, $limit, $onError)
    {
        $response = [];
        $resultset = [];
        $puntoAtencion = $this->puntoAtencionRepository->find($id);
        $validateResultado = $this->puntoAtencionValidator->verificaPuntoAtencion($puntoAtencion);

        if (! $validateResultado->hasError()) {
            $response = $this->tramiteRepository->findByPuntoAtencion($id, $offset, $limit);
            $resultset = [
                'resultset' => [
                    'count' => $this->tramiteRepository->getTotalByPuntoAtencion($id),
                    'offset' => $offset,
                    'limit' => $limit
                ]
            ];
        }
        return $this->processError(
            $validateResultado,
            function () use ($resultset, $response) {
                return $this->respuestaData($resultset, $response);
            },
            $onError
        );
    }

    /**
     * Buscar puntos de atención por nombre del punto, dirección de punto o nombre del area
     *
     * @param array $params arreglo con los datos para la búsqueda
     * @param integer $limit Cantidad máxima de registros a retornar
     * @param integer $offset Cantidad de registros a saltar
     * @param callback $onError Callback para devolver respuesta fallida
     * @return mixed
     */
    public function search($params, $offset, $limit, $onError)
    {
        $response = [];
        $resultset = [];
        $validateResultado = $this->puntoAtencionValidator->validarSearch($params);

        if (! $validateResultado->hasError()) {
            $response = $this->puntoAtencionRepository->search($params['q'], $offset, $limit);
            $resultset = [
                'resultset' => [
                    'count' => $this->puntoAtencionRepository->getTotalSearch($params['q']),
                    'offset' => $offset,
                    'limit' => $limit
                ]
            ];
        }

        return $this->processError(
            $validateResultado,
            function () use ($resultset, $response) {
                return $this->respuestaData($resultset, $response);
            },
            $onError
        );
    }

    /**
     * Obtener un punto de atencion
     *
     * @param integer $id Identificador único del punto de atención
     * @param callback $sucess Callback para devolver respuesta exitosa
     * @param callback $error Callback para devolver respuesta fallida
     * @return mixed
     */
    public function get($id, $success, $error)
    {
        $result = [];
        $puntoAtencion = $this->puntoAtencionRepository->find($id);
        $validateResultado = $this->puntoAtencionValidator->validarEntidad($puntoAtencion, "El punto de atención no existe");
        if (!$validateResultado->hasError()) {
            $result = [
                'id' => $puntoAtencion->getId(),
                'nombre' => $puntoAtencion->getNombre(),
                'direccion' => $puntoAtencion->getDireccion(),
                'latitud' => $puntoAtencion->getLatitud(),
                'longitud' => $puntoAtencion->getLongitud(),
                'estado' => $puntoAtencion->getEstado(),
                'area' => [
                    'id' => $puntoAtencion->getArea()->getid(),
                    'nombre' => $puntoAtencion->getArea()->getNombre()
                ],
                'organismo'=> [
                    'id' => $puntoAtencion->getArea()->getOrganismo()->getId(),
                    'nombre' => $puntoAtencion->getArea()->getOrganismo()->getNombre()
                ],
                'provincia' => [
                    'id' => $puntoAtencion->getProvincia()->getId(),
                    'nombre' => $puntoAtencion->getProvincia()->getNombre()
                ],
                'localidad' => [
                    'id' => $puntoAtencion->getLocalidad()->getId(),
                    'nombre' => $puntoAtencion->getLocalidad()->getNombre()
                ],
                'tramites' => []
            ];
            // tramites del punto de atención
            foreach ($puntoAtencion->getTramites() as $puntoTramite) {
                /** @var Tramite $tramite */
                $tramite = $puntoTramite->getTramite();
                $result['tramites'][] = [
                    'id' => $tramite->getId(),
                    'nombre' => $tramite->getNombre()
                ];
            }
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
     * Crear nuevo punto de atención
     *
     * @param array $params Array con los datos para crear un nuevo punto de atención
     * @param callback $sucess Callback para devolver respuesta exitosa
     * @param callback $error Callback para devolver respuesta fallida
     * @return mixed
     */
    public function create($params, $success, $error)
    {
        $diaNoLaborableSync = new DiaNoLaborableSync(
            $this->feriadoNacionalRepository,
            $this->diaNoLaborableRepository
        );
        $puntoAtencionFactory = new PuntoAtencionFactory(
            $this->provinciaRepository,
            $this->localidadRepository,
            $this->areaRepository,
            $this->puntoAtencionValidator,
            $this->tramiteRepository
        );
        $this->puntoAtencionRepository->beginTransaction();
        $validateResult = $puntoAtencionFactory->create($params);
        if (! $validateResult->hasError()) {
            $puntoAtencion = $this->puntoAtencionRepository->save($validateResult->getEntity());
            //Se agrega el dia no laborable de todos los feriados para el pda
            $diaNoLaborableSync->add($puntoAtencion);

            if (isset($params['tramites'])) {
                foreach ($params['tramites'] as $tramiteId) {
                    $tramite = $this->tramiteRepository->find($tramiteId);
                    if ($tramite) {
                        $puntoTramite = new PuntoTramite(
                            $puntoAtencion,
                            $tramite
                        );
                        $this->puntoTramiteRepository->save($puntoTramite);
                        $puntoAtencion->addTramite($puntoTramite);
                    }
                }
            }

            $validateResult = $this->puntoAtencionIntegration->agregarPuntoAtencion($puntoAtencion);
            if ($validateResult->hasError()) {
                $this->puntoAtencionRepository->rollback();
            }else {
                $this->puntoAtencionRepository->commit();
            }
        }

        return $this->processResult(
            $validateResult,
            function () use ($success, $puntoAtencion) {
                return call_user_func($success, $puntoAtencion);
            },
            $error
        );
    }

    /**
     * Modificar punto de atención
     *
     * @param array $params Array con los datos para modificar un punto de atención
     * @param integer $id Identificador único de un punto de atención
     * @param callback $sucess Callback para devolver respuesta exitosa
     * @param callback $error Callback para devolver respuesta fallida
     * @return mixed
     */
    public function edit($params, $id, $success, $error)
    {

        $puntoAtencionSync = new PuntoAtencionSync(
            $this->puntoAtencionRepository,
            $this->areaRepository,
            $this->tramiteRepository,
            $this->provinciaRepository,
            $this->localidadRepository,
            $this->puntoAtencionValidator,
            $this->diaNoLaborableRepository,
            $this->puntoTramiteRepository
        );


        $this->puntoAtencionRepository->beginTransaction();
        $validateResult = $puntoAtencionSync->edit($id, $params);

        if (! $validateResult->hasError()) {
            $this->puntoAtencionRepository->flush();
            $validateResult = $this->puntoAtencionIntegration->editarPuntoAtencion($id, $params);
            if ($validateResult->hasError()) {
                $this->puntoAtencionRepository->rollback();
            }else {
                $this->puntoAtencionRepository->commit();
            }
        }

        return $this->processResult(
            $validateResult,
            function () use ($success,$validateResult) {
                return call_user_func($success, $validateResult->getEntity());
            },
            $error
        );
    }

    /**
     * Eliminar punto de atención
     *
     * @param integer $id Identificador único de un punto de atención
     * @param callback $sucess Callback para devolver respuesta exitosa
     * @param callback $error Callback para devolver respuesta fallida
     * @return mixed
     */
    public function delete($id, $success, $error)
    {
        $puntoAtencionSync = new PuntoAtencionSync(
            $this->puntoAtencionRepository,
            $this->areaRepository,
            $this->tramiteRepository,
            $this->provinciaRepository,
            $this->localidadRepository,
            $this->puntoAtencionValidator,
            $this->diaNoLaborableRepository,
            $this->puntoTramiteRepository
        );

        $this->puntoAtencionRepository->beginTransaction();
        $errors = [];
        $validateResult = $puntoAtencionSync->delete($id);
        if (! $validateResult->hasError()) {
            $this->puntoAtencionRepository->remove($validateResult->getEntity());
            $validateResult = $this->puntoAtencionIntegration->eliminarPuntoAtencion($id);
            if ($validateResult->hasError()) {
                $this->puntoAtencionRepository->rollback();
            }else {
                $this->puntoAtencionRepository->commit();
            }
        }

        return $this->processResult(
            $validateResult,
            function () use ($success,$validateResult) {
                return call_user_func($success, $validateResult->getEntity());
            },
            $error
        );
    }

    /**
     * Asigna tramites a un punto de atención
     *
     * @param array $params Array con los datos para modificar un punto de atención
     * @param integer $id Identificador único de un punto de atención
     * @param callback $sucess Callback para devolver respuesta exitosa
     * @param callback $error Callback para devolver respuesta fallida
     * @return mixed
     */
    public function setTramites($params, $id, $success, $error)
    {
        $puntoAtencionSync = new PuntoAtencionSync(
            $this->puntoAtencionRepository,
            $this->areaRepository,
            $this->tramiteRepository,
            $this->provinciaRepository,
            $this->localidadRepository,
            $this->puntoAtencionValidator,
            $this->diaNoLaborableRepository,
            $this->puntoTramiteRepository
        );

        $this->puntoAtencionRepository->beginTransaction();
        $validateResult = $puntoAtencionSync->setTramites($id, $params);

        if ($validateResult->hasError()) {
            $this->puntoAtencionRepository->rollback();
        }

        return $this->processResult(
            $validateResult,
            function () use ($success) {
                $this->puntoAtencionRepository->commit();
                return call_user_func($success, $this->puntoAtencionRepository->flush());
            },
            $error
        );
    }

    /**
     * Lista los trámites disponibles de un punto de atención que no pertenecen a un grupo de trámites
     *
     * @param integer $puntoAtencionId Identificador único de un punto de atención
     * @param integer $limit Cantidad máxima de registros a retornar
     * @param integer $offset Cantidad de registros a saltar
     * @param callback $onError Callback para devolver respuesta fallida
     * @return mixed
     */
    public function findTramitesDisponiblesPaginate($puntoAtencionid, $offset, $limit, $onError)
    {
        $response = [];
        $puntoAtencion = $this->puntoAtencionRepository->find($puntoAtencionid);
        $validateResultado = $this->puntoAtencionValidator->verificaPuntoAtencion($puntoAtencion);
        if (! $validateResultado->hasError()) {
            $puntoAtencionSync = new PuntoAtencionSync(
                $this->puntoAtencionRepository,
                $this->areaRepository,
                $this->tramiteRepository,
                $this->provinciaRepository,
                $this->localidadRepository,
                $this->puntoAtencionValidator,
                $this->diaNoLaborableRepository,
                $this->puntoTramiteRepository
            );
            $listaTramitesDisponibles = $puntoAtencionSync->findTramitesDisponibles($puntoAtencion, $puntoAtencionid);
            $resultset = [
                'resultset' => [
                    'count'  => count($listaTramitesDisponibles),
                    'offset' => $offset,
                    'limit'  => $limit
                ]
            ];
            $response = $this->respuestaData($resultset, $listaTramitesDisponibles);
        }

        return $this->processError(
            $validateResultado,
            function () use ($response) {
                return $response;
            },
            $onError
        );
    }

     /**
     * Obtener cantidad de turnos de un punto de atención
     *
     * @param integer $id Identificador único de un punto de atención
     * @param date $fecha fecha a buscar
     * @param callback $onError Callback para devolver respuesta fallida
     * @return mixed
     */
    public function getCantidadTurnos($id, $fecha, $onError)
    {

        $response = [];
        $puntoAtencion = $this->puntoAtencionRepository->find($id);

        $validateResultado = $this->puntoAtencionValidator->verificaPuntoAtencion($puntoAtencion);

        if (! $validateResultado->hasError()) {
            $fecha = new \DateTime($fecha);

            $turnos = $puntoAtencion->getTurnos();

            $turnosFecha = $turnos->filter(
                function ($turno) use ($fecha) {
                    if ($turno->getFecha() == $fecha && $turno->getEstado() == Turno::ESTADO_ASIGNADO) {
                        return $turno;
                    }
                }
            );

            $response = $turnosFecha->count();
        }

        return $this->processError(
            $validateResultado,
            function () use ($response) {
                return $this->respuestaData([], $response);
            },
            $onError
        );
    }

     /**
     * Habilita un día No habil como día laborable.
     *
     * @param array $params arreglo con los datos del día
     * @param integer $id Identificador único del punto de atención
     * @param callback $sucess Callback para devolver respuesta exitosa
     * @param callback $error Callback para devolver respuesta fallida
     * @return mixed
     */
    public function habilitarFecha($params, $id, $success, $error)
    {
        $puntoAtencionSync = new PuntoAtencionSync(
            $this->puntoAtencionRepository,
            $this->areaRepository,
            $this->tramiteRepository,
            $this->provinciaRepository,
            $this->localidadRepository,
            $this->puntoAtencionValidator,
            $this->diaNoLaborableRepository,
            $this->puntoTramiteRepository
        );

        $validateResult = $puntoAtencionSync->habilitarFecha($id, $params);

        return $this->processResult(
            $validateResult,
            function () use ($success) {
                return call_user_func($success, $this->puntoAtencionRepository->flush());
            },
            $error
        );
    }

    /**
     * Agrega un día no habil a un punto de atención
     *
     * @param array $params arreglo con los datos del día
     * @param integer $puntoAtencionId Identificador único del punto de atención
     * @param callback $sucess Callback para devolver respuesta exitosa
     * @param callback $error Callback para devolver respuesta fallida
     * @return mixed
     */
    public function agregarDiaNoHabil($params, $id, $success, $error)
    {
        $puntoAtencionSync = new PuntoAtencionSync(
            $this->puntoAtencionRepository,
            $this->areaRepository,
            $this->tramiteRepository,
            $this->provinciaRepository,
            $this->localidadRepository,
            $this->puntoAtencionValidator,
            $this->diaNoLaborableRepository,
            $this->puntoTramiteRepository
        );

        $validateResult = $puntoAtencionSync->agregarDiaNoHabil($id, $params);

        return $this->processResult(
            $validateResult,
            function () use ($success) {
                return call_user_func($success, $this->puntoAtencionRepository->flush());
            },
            $error
        );
    }

    /**
     * Listado de todos los dias no laborables
     *
     * @param integer $id Identificador único del punto de atención
     * @param callback $onError Callback para devolver respuesta fallida
     * @return mixed
     */
    public function getDiasNoLaborables($id, $onError)
    {
        $result = [];
        $puntoAtencion = $this->puntoAtencionRepository->find($id);
        $validateResultado  = $this->puntoAtencionValidator->verificaPuntoAtencion($puntoAtencion);

        if (! $validateResultado->hasError()) {
            $diasNoLaborables = $puntoAtencion->getDiasNoLaborables();

            foreach ($diasNoLaborables as $diaNoLaboral) {
                $result[] = $diaNoLaboral->getFecha()->format('Y-m-d');
            }
        }

        return $this->processError(
            $validateResultado,
            function () use ($result) {
                return $this->respuestaData([], $result);
            },
            $onError
        );
    }

    /**
     * Listado de ids de puntos de atención
     *
     * @param string $authorization token del usuario logueado
     * @param callback $onError Callback para devolver respuesta fallida
     * @return mixed
     */
    public function getIdsPuntoAtencion($authorization, $onError)
    {
        $resultset = [];
        $result = [];
        $validateResultado =
            $this->rolesServices->getUsuario($authorization);

        if (!$validateResultado->hasError()) {
            $usuario = $validateResultado->getEntity();
            $result = $this->puntoAtencionRepository->findAllPaginate(
                null,
                null,
                $usuario
            );

            $result = array_column($result, 'id');
        }

        return $this->processError(
            $validateResultado,
            function () use ($result, $resultset) {
                return $this->respuestaData($resultset, $result);
            },
            $onError
        );

    }

    /**
     * Asignar visibilidad
     *
     * @param integer $puntoAtencionId Identificador único del punto de atención
     * @param integer $tramiteId Identificador único del trámite
     * @param array $params arreglo con el estado
     * @param callback $success Callback para devolver respuesta exitosa
     * @param callback $error Callback para devolver respuesta fallida
     * @return mixed
     */
    public function setVisibilidad($puntoAtencionId, $tramiteId, $params, $success, $error)
    {
        $puntoAtencion = $this->puntoAtencionRepository->find($puntoAtencionId);
        $tramite = $this->tramiteRepository->find($tramiteId);

        $validateResultado = $this->puntoAtencionValidator->validarSetVisibilidad($puntoAtencion, $tramite, $params);
        if (!$validateResultado->hasError()) {
            /** @var PuntoTramite $puntoTramite */
            $puntoTramite = $this->puntoTramiteRepository->findOneBy([
                'puntoAtencion' => $puntoAtencion,
                'tramite' => $tramite
            ]);

            if ($puntoTramite) {
                $puntoTramite->setEstado($params['estado']);
                $validateResultado = new ValidateResultado($puntoTramite, []);
            } else {
                $validateResultado = new ValidateResultado(null, [
                    "El tramite {$tramite->getNombre()} no puede ser realizado por el punto de atención"
                ]);
            }
        }

        return $this->processError(
            $validateResultado,
            function () use ($success) {
                $this->puntoTramiteRepository->flush();
                return call_user_func($success);
            },
            $error
        );
    }

    /**
     * Inhabilitar un día
     *
     * @param integer $puntoAtencionId identificador único del punto de atención
     * @param  array $params arreglo con los datos de fecha
     * @param callback $success Callback para devolver respuesta exitosa
     * @param callback $error Callback para devolver respuesta fallida
     * @return mixed
     */
    public function inhabilitarDia($puntoAtencionId, $params, $success, $error)
    {
        $puntoAtencion = $this->puntoAtencionRepository->find($puntoAtencionId);

        // Validamos que el punto de atención existe
        $validateResultado = $this->puntoAtencionValidator->validarEntidad($puntoAtencion, "El punto de atención no existe");

        if (!$validateResultado->hasError()) {
            $puntoAtencionSync = new PuntoAtencionSync(
                $this->puntoAtencionRepository,
                $this->areaRepository,
                $this->tramiteRepository,
                $this->provinciaRepository,
                $this->localidadRepository,
                $this->puntoAtencionValidator,
                $this->diaNoLaborableRepository,
                $this->puntoTramiteRepository
            );

            $validateResultado = $puntoAtencionSync->inhabilitarDia($puntoAtencion, $params);
        }

        return $this->processError(
            $validateResultado,
            function () use ($success) {
                return call_user_func($success);
            },
            $error
        );
    }
    
    /**
     * Editar PuntoTramite
     *
     * @param integer $puntoAtencionId Identificador único del punto de atención
     * @param integer $tramiteId Identificador único del trámite
     * @param array $params arreglo con el estado
     * @param callback $success Callback para devolver respuesta exitosa
     * @param callback $error Callback para devolver respuesta fallida
     * @return mixed
     */
    public function editarPuntoTramite($puntoAtencionId, $tramiteId, $params, $success, $error)
    {
        $puntoAtencion = $this->puntoAtencionRepository->find($puntoAtencionId);
        $tramite = $this->tramiteRepository->find($tramiteId);

        $validateResultado = $this->puntoAtencionValidator->validarEditarPuntoTramite($puntoAtencion, $tramite, $params);
        if (!$validateResultado->hasError()) {
            /** @var ApiV1Bundle\Entity\PuntoTramite $puntoTramite */
            $puntoTramite = $this->puntoTramiteRepository->findOneBy([
                'puntoAtencion' => $puntoAtencion,
                'tramite' => $tramite
            ]);
                
            if ($puntoTramite) { 
                $puntoTramite->setMultiple( $params['multiple']);
                $puntoTramite->setMultipleHorizonte( $params['multiple_horizonte']);
                $puntoTramite->setMultipleMax( $params['multiple_max']);
                $puntoTramite->setPermiteOtro( $params['permite_otro']);
                $puntoTramite->setPermiteOtroCantidad( $params['permite_otro_cantidad']);
                $puntoTramite->setMultiturno( $params['multiturno']);
                $puntoTramite->setMultiturnoCantidad( $params['multiturno_cantidad']);
                $puntoTramite->setPermitePrioridad( $params['permite_prioridad']);
                $puntoTramite->setDeshabilitarHoy( $params['deshabilitar_hoy']);
                $validateResultado = new ValidateResultado($puntoTramite, []);
            } else {
                $validateResultado = new ValidateResultado(null, [
                    "El tramite {$tramite->getNombre()} no puede ser realizado por el punto de atención"
                ]);
            }
        }

        return $this->processError(
            $validateResultado,
            function () use ($success) {
                $this->puntoTramiteRepository->flush();
                return call_user_func($success);
            },
            $error
        );
    }
    
    
         /**
     * Obtener un Punto_tramite dado su ID de tramite y el punto de atención
     *
     * @param integer $tramiteId Identificador único del trámite del que se quiere obtener información
     * @param integer $puntoAtencionId Identificador único del punto de atención
     * @return mixed
     */
    public function getPuntoTramiteItem($puntoAtencionId, $tramiteId)
    {
        $puntoAtencion = $this->puntoAtencionRepository->find($puntoAtencionId);
        $tramite = $this->tramiteRepository->find($tramiteId);
        /** @var PuntoTramite $puntoTramite */
        $puntoTramite = $this->puntoTramiteRepository->findOneBy([
            'puntoAtencion' => $puntoAtencion,
            'tramite' => $tramite
        ]);
        
        if ($puntoTramite) {
            return $this->respuestaData([], [
                'multiple'=>$puntoTramite->getMultiple(),
                'multiple_horizonte'=>$puntoTramite->getMultipleHorizonte(),
                'multiple_max'=>$puntoTramite->getMultipleMax(),
                'permite_otro'=>$puntoTramite->getPermiteOtro(),
                'permite_otro_cantidad'=>$puntoTramite->getPermiteOtroCantidad(),
                'permite_prioridad'=>$puntoTramite->getPermitePrioridad(),
                'deshabilitar_hoy'=>$puntoTramite->getDeshabilitarHoy(),
                'tramite'=>$puntoTramite->getTramite()->getNombre(),
                'multiturno'=>$puntoTramite->getMultiturno(),
                'multiturno_cantidad'=>$puntoTramite->getMultiturnoCantidad()
            ]);
        }
        return $this->respuestaData([], null);
    }
    
    
}

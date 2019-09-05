<?php
namespace ApiV1Bundle\ApplicationServices;

use SNT\Domain\Services\Parser;
use ApiV1Bundle\Entity\DiaNoLaborable;
use ApiV1Bundle\Entity\Factory\DatosTurnoFactory;
use ApiV1Bundle\Entity\Factory\TurnoFactory;
use ApiV1Bundle\Entity\Sync\TurnoSync;
use ApiV1Bundle\Entity\Turno;
use ApiV1Bundle\Entity\Validator\CommunicationValidator;
use ApiV1Bundle\Entity\Validator\TurnoValidator;
use ApiV1Bundle\ExternalServices\NotificationsExternalService;
use ApiV1Bundle\Helper\FormHelper;
use ApiV1Bundle\Helper\ServicesHelper;
use ApiV1Bundle\Repository\DiaNoLaborableRepository;
use ApiV1Bundle\Repository\DiaNoLaborableTramiteRepository;
use ApiV1Bundle\Repository\GrupoTramiteRepository;
use ApiV1Bundle\Repository\PuntoAtencionRepository;
use ApiV1Bundle\Repository\TramiteRepository;
use ApiV1Bundle\Repository\TurnoRepository;
use Monolog\Logger;
use Symfony\Component\DependencyInjection\Container;
use Unirest\Exception;
use ApiV1Bundle\Repository\DisponibilidadRepository;
use ApiV1Bundle\Repository\ReasignacionRepository;
use ApiV1Bundle\Entity\Reasignacion;
use ApiV1Bundle\ApplicationServices\RedisServices;

/**
 * Class TurnoServices
 * @package ApiV1Bundle\ApplicationServices
 */

class TurnoServices extends SNTServices
{
    /** @var TurnoRepository  */
    private $turnoRepository;
    /** @var PuntoAtencionRepository  */
    private $puntoAtencionRepository;
    /** @var TramiteRepository  */
    private $tramiteRepository;
    /** @var GrupoTramiteRepository  */
    private $grupoTramiteRepository;
    /** @var TurnoValidator  */
    private $turnoValidator;
    /** @var NotificationsExternalService  */
    private $notificationsService;
    /** @var DiaNoLaborableRepository  */
    private $diaNoLaborableRepository;
    /** @var DiaNoLaborableTramiteRepository  */
    private $diaNoLaborableTramiteRepository;
    /** @var CommunicationValidator  */
    private $communicationValidator;
    private $disponibilidadRepository;
    private $logger;
    
    /** @var Parser */
    private $parser;
    /** @var RedisServices  */
    private $redisServices;
    
    /**
     * TurnoServices constructor.
     *
     * @param Container $container
     * @param TurnoRepository $turnoRepository
     * @param PuntoAtencionRepository $puntoAtencionRepository
     * @param TramiteRepository $tramiteRepository
     * @param GrupoTramiteRepository $grupoTramiteRepository
     * @param TurnoValidator $turnoValidator
     * @param NotificationsExternalService $notificationsService
     * @param DiaNoLaborableRepository $diaNoLaborableRepository
     * @param DiaNoLaborableTramiteRepository $diaNoLaborableTramiteRepository
     * @param CommunicationValidator $communicationValidator
     * @param Parser $parserService
     * @param RedisServices $redisServices
     */
    public function __construct(
        Container $container,
        TurnoRepository $turnoRepository,
        PuntoAtencionRepository $puntoAtencionRepository,
        TramiteRepository $tramiteRepository,
        GrupoTramiteRepository $grupoTramiteRepository,
        TurnoValidator $turnoValidator,
        NotificationsExternalService $notificationsService,
        DiaNoLaborableRepository $diaNoLaborableRepository,
        DiaNoLaborableTramiteRepository $diaNoLaborableTramiteRepository,
        CommunicationValidator $communicationValidator,
        DisponibilidadRepository $disponibilidadRepository,
        Parser $parserService,
        RedisServices $redisServices
    ) {
        parent::__construct($container);
        $this->turnoRepository = $turnoRepository;
        $this->puntoAtencionRepository = $puntoAtencionRepository;
        $this->tramiteRepository = $tramiteRepository;
        $this->grupoTramiteRepository = $grupoTramiteRepository;
        $this->turnoValidator = $turnoValidator;
        $this->notificationsService = $notificationsService;
        $this->diaNoLaborableRepository = $diaNoLaborableRepository;
        $this->diaNoLaborableTramiteRepository = $diaNoLaborableTramiteRepository;
        $this->communicationValidator = $communicationValidator;
        $this->disponibilidadRepository = $disponibilidadRepository;
        $this->parser = $parserService;
        $this->redisServices = $redisServices;
    }

    /**
     * Listar Turnos por tramite y por punto de atencion
     *
     * @param integer $puntoAtencionId Identificador único del punto de atención
     * @param integer $tramiteId Identificador único del tramite
     * @param integer $limit Cantidad máxima de registros a retornar
     * @param integer $offset Cantidad de registros a saltar
     * @return mixed
     */
    public function findAllPaginate($limit, $offset, $where = [])
    {
        $result = $this->turnoRepository->findAllPaginate($offset, $limit, $where);
    
        $resultset = [
            'resultset' => [
                'count' => $this->turnoRepository->getTotal($where),
                'offset' => $offset,
                'limit' => $limit
            ]
        ];
        
        return $this->respuestaData($resultset, $result);
    }

    /**
     * Listar turnos de un ciudadano
     *
     * @param array $params arreglo con los datos del ciudadano (dni)
     * @param callback $onError Callback para devolver respuesta fallida
     * @return mixed
     * @throws \Doctrine\ORM\NoResultException
     * @throws \Doctrine\ORM\NonUniqueResultException
     */
    public function findAllCiudadanoPorDni($params, $onError)
    {
        $result = [];
        $resultset = [];

        $validateResultado = $this->turnoValidator->validarFindAllCiudadanosDni($params);

        if (!$validateResultado->hasError()) {
            $dni = $params['dni'];
            $offset = isset($params['offset']) ? $params['offset'] : 0;
            $limit = isset($params['limit']) ? $params['limit'] : 10;

            $result = $this->turnoRepository->findTurnosPorCiudadanoDni($dni, $offset, $limit);
            foreach ($result as &$turnoArray) {
                $turno = $this->turnoRepository->find($turnoArray['id']);
                $turnoArray['fecha'] = $turno->getFecha()->format('Y-m-d');
                $turnoArray['hora'] = $turno->getHora()->format('H:i');
                $turnoArray['tramite'] = [
                    'id' => $turno->getTramite()->getId(),
                    'nombre' => $turno->getTramite()->getNombre()
                ];
            }

            $resultset = [
                'resultset' => [
                    'count' => $this->turnoRepository->getTotalCiudadanoDni($dni),
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
     * Listar turnos de un ciudadano
     *
     * @param array $params arreglo con los datos del ciudadano (cuil)
     * @param callback $onError Callback para devolver respuesta fallida
     * @return mixed
     * @throws \Doctrine\ORM\NoResultException
     * @throws \Doctrine\ORM\NonUniqueResultException
     */
    public function findAllCiudadano($params, $onError)
    {
        $result = [];
        $resultset = [];

        $validateResultado = $this->turnoValidator->validarFindAllCiudadanos($params);
        if (! $validateResultado->hasError()) {
            $documento = ServicesHelper::buildValidDocument($params['cuil']);
            $offset = isset($params['offset']) ? $params['offset'] : 0;
            $limit = isset($params['limit']) ? $params['limit'] : 10;

            $result = $this->turnoRepository->findTurnosPorCiudadano($documento, $offset, $limit);
            foreach ($result as &$turnoArray) {
                $turno = $this->turnoRepository->find($turnoArray['id']);
                $turnoArray['fecha'] = $turno->getFecha()->format('Y-m-d');
                $turnoArray['hora'] = $turno->getHora()->format('H:i');
                $turnoArray['tramite'] = [
                    'id' => $turno->getTramite()->getId(),
                    'nombre' => $turno->getTramite()->getNombre()
                ];
            }

            $resultset = [
                'resultset' => [
                    'count' => $this->turnoRepository->getTotalCiudadano($documento),
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
     * Buscar turno
     *
     * @param array $params arreglo con los datos para la búsqueda (cuil y código de turno)
     * @param callback $onError Callback para devolver respuesta fallida
     * @return mixed
     */
    public function search($params, $onError)
    {
        $response = [];
        $validateResultado = $this->turnoValidator->validarSearch($params);

        if (! $validateResultado->hasError()) {
            $response = $this->getTurno($validateResultado->getEntity());
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
     * Buscar turno por código o Cuil desde snc
     *
     * @param array $params arreglo con los datos para la búsqueda (cuil y código de turno)
     * @param callback $onError Callback para devolver respuesta fallida
     * @return mixed
     * @throws \Doctrine\ORM\NoResultException
     * @throws \Doctrine\ORM\NonUniqueResultException
     */
    public function searchByCodigoCuil($params, $onError)
    {
        $response = [];
        $resultset = [];
        $count = 0;
        // validamos la firma digital
        $validateResultado = $this->communicationValidator->validateSNCRequest($params);

        if (! $validateResultado->hasError()) {
            $validateResultado = $this->turnoValidator->validarSearchByCodigo($params);
            if (! $validateResultado->hasError()) {
                $codigo = ServicesHelper::buildValidDocument($params['codigo']);
                $puntoAtencionId = $params['puntoatencionid'];
                $offset = isset($params['offset']) ? $params['offset'] : 0;
                $limit = isset($params['limit']) ? $params['limit'] : 10;

                $turnos = $this->turnoRepository->searchByCodigoOCuil(
                    $codigo,
                    $puntoAtencionId,
                    $offset,
                    $limit
                );
                $count = $this->turnoRepository->getTotalByCodigoOCuil($codigo, $puntoAtencionId);

                $resultset = [
                    'resultset' => [
                        'count' => $count,
                        'offset' => $offset,
                        'limit' => $limit
                    ]
                ];

                $response = $this->getTurnos($turnos);
            }
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
     * Obtener un turno
     *
     * @param integer $id Identificador único del turno
     * @return mixed
     */
    public function get($id)
    {
        $turno = $this->turnoRepository->find($id);
        
        if ($turno) {
            $response = $this->getTurno($turno);
            return $this->respuestaData([], $response);
        }
        return $this->respuestaData([], null);
    }

    /**
     * Obtenemos un turnos desde SNC
     *
     * @param array $params arreglo con los datos para la búsqueda
     * @return mixed
     */
    public function getTurnoSnc($params)
    {
        // validamos la firma digital
        $validateResultado = $this->communicationValidator->validateSNCRequest($params);
        if (! $validateResultado->hasError()) {
            $turno = $this->turnoRepository->find($params['turno_id']);
            if ($turno && $turno->getEstado() == 1) {
                $response = $this->getTurno($turno);
                // cambiamos el nombre de cuil a documento
                $documento = $response['datos_turno']['cuil'];
                $response['datos_turno']['documento'] = $documento;
                $response['datos_turno']['campos']['documento'] = $documento;
                unset($response['datos_turno']['cuil']);
                unset($response['datos_turno']['campos']['cuil']);

                return $this->respuestaData([], $response);
            }
        }
        return $this->respuestaData([], null);
    }

    /**
     * Obtiene el último turno del Punto de atención
     *
     * @param integer $puntoAtencionId Identificador único del punto de atención
     * @param integer $grupoTramitesId Identificador único del grupo trámite
     * @param integer $tramiteId Identificador único del trámite
     * @param callback $onError Callback para devolver respuesta fallida
     * @return mixed
     */
    public function getUltimoTurno($puntoAtencionId, $grupoTramitesId, $tramiteId, $onError)
    {
        $response = [];

        $puntoAtencion = null;
        if ($puntoAtencionId) {
            $puntoAtencion = $this->puntoAtencionRepository->find($puntoAtencionId);
        }
        $grupoTramites = null;
        if ($grupoTramitesId) {
            $grupoTramites = $this->grupoTramiteRepository->find($grupoTramitesId);
        }
        $tramite = null;
        if ($tramiteId) {
            $tramite = $this->tramiteRepository->find($tramiteId);
        }

        $validateResultado = $this->turnoValidator->validarUltimoTurno(
            $tramiteId,
            $tramite,
            $puntoAtencionId,
            $puntoAtencion,
            $grupoTramitesId,
            $grupoTramites
        );
        if (! $validateResultado->hasError()) {
            $ultimoTurno = $this->turnoRepository->findUltimoTurno($puntoAtencionId, $grupoTramitesId, $tramiteId);
            if ($ultimoTurno) {
                $response = [
                    'fecha' => $ultimoTurno['fecha']->format('Y-m-d')
                ];
            }
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
     * Crear un turno
     *
     * @param array $params Array con los datos para crear un turno
     * @param callback $success Callback para devolver respuesta exitosa
     * @param callback $error Callback para devolver respuesta fallida
     * @return mixed
     */
    public function create($params, $user, $success, $error)
    {
        $turnoFactory = new TurnoFactory(
            $this->puntoAtencionRepository,
            $this->tramiteRepository,
            $this->turnoValidator,
            $this->notificationsService
        );
        $validateResult = $turnoFactory->create($params, $user);
        return $this->processResult(
            $validateResult,
            function ($entity) use ($success) {
                return call_user_func($success, $this->turnoRepository->save($entity));
            },
            $error
        );
    }

    /**
     * Modificar un turno
     *
     * @param array $params Array con los datos para modificara un turno
     * @param integer $id Identificador único del turno
     * @param callback $success Callback para devolver respuesta exitosa
     * @param callback $error Callback para devolver respuesta fallida
     * @return mixed
     */
    public function edit($params, $id, $success, $error)
    {
        $turnoSync = new TurnoSync(
            $this->turnoRepository,
            new DatosTurnoFactory(),
            $this->turnoValidator,
            $this->notificationsService
        );

        $validateResult = $turnoSync->edit($id, $params);
       
        $notification = null;
        if (! $validateResult->hasError()) {
            $notification = $this->enviarEmailCiudadano($id, $params, "turnos");
        }
        $this->redisDelTurno($validateResult);
        return $this->processResult(
            $validateResult,
            function ($entity) use ($success, $notification) {
                return call_user_func_array($success, [$this->tramiteRepository->flush(), $notification]);
            },
            $error
        );
    }

    /**
     * Cancelar un turno
     *
     * @param string $codigo código del turno
     * @param callback $success Callback para devolver respuesta exitosa
     * @param callback $error Callback para devolver respuesta fallida
     * @return mixed
     */
    public function cancel($codigo, $success, $error)
    {
        $turnoSync = new TurnoSync(
            $this->turnoRepository,
            new DatosTurnoFactory(),
            $this->turnoValidator,
            $this->notificationsService
        );
        $validateResult = $turnoSync->cancel($codigo);
        $this->redisDelTurno($validateResult);
        return $this->processResult(
            $validateResult,
            function ($entity) use ($success) {
                return call_user_func($success, $this->tramiteRepository->flush());
            },
            $error
        );
    }

    /**
     * Eliminar un turno
     *
     * @param integer $id Identificador único del turno
     * @param callback $success Callback para devolver respuesta exitosa
     * @param callback $error Callback para devolver respuesta fallida
     * @return mixed
     */
    public function delete($id, $success, $error)
    {
        $turnoSync = new TurnoSync(
            $this->turnoRepository,
            new DatosTurnoFactory(),
            $this->turnoValidator,
            $this->notificationsService
        );
        $validateResult = $turnoSync->delete($id);
        $this->redisDelTurno($validateResult);
        return $this->processResult(
            $validateResult,
            function ($entity) use ($success) {
                return call_user_func($success, $this->turnoRepository->remove($entity));
            },
            $error
        );
    }

    /**
     * Enviar mail de turno al ciudadano
     *
     * @param integer $id Identificador único del turno
     * @param array $params arreglo con los datos del turno
     * @return mixed
     */
    public function enviarEmailCiudadano($id, $params, $template)
    {
        $turno = $this->turnoRepository->find($id);
        if ($turno) {
            $codigo = ServicesHelper::obtenerCodigoSimple($turno->getCodigo());
            $cancelarTurnoUrl = $this->notificationsService->getEmailUrl('cancelar');
            $cancelarTurnoUrl .= 'codigo/' . $codigo . '/cuil/' . $params['campos']['cuil'];

            $tipoDocumento = $turno->getTramite()->getExcepcional() ? 'Documento extranjero' : 'CUIL';

            $turnoData = [
                'nombre' => $params['campos']['nombre'],
                'apellido' => $params['campos']['apellido'],
                'tipo_documento' => $tipoDocumento,
                'cuil' => $params['campos']['cuil'],
                'codigo' => $codigo,
                'fecha' => $turno->getFecha()->format('d-m-Y'),
                'horario' => $turno->getHora()->format('H:i'),
                'tramite' => $turno->getTramite()->getNombre(),
                'lugar' => $turno->getPuntoAtencion()->getNombre(),
                'direccion' => $turno->getPuntoAtencion()->getDireccion() . '. ' .
                    $turno->getPuntoAtencion()->getProvincia()->getNombre() . ', ' .
                    $turno->getPuntoAtencion()->getLocalidad()->getNombre(),
                'requisitos' => $this->parser->render($turno->getTramite()->getRequisitos()),
                'email' => $params['campos']['email'],
                'cancelar_url' => $cancelarTurnoUrl,
            ];
            try {
                return $this->notificationsService->enviarNotificacion(
                    $this->notificationsService->getEmailTemplate($template),
                    $params['campos']['email'],
                    $params['campos']['cuil'],
                    $turnoData
                );
            } catch (Exception $exception) {
                // nada
                return null;
            }
        }
        return null;
    }

    /**
     * Obtener Listado de turnos por punto de atencion y fecha
     *
     * @param int $puntoAtencionId Identificador único del punto de atencion
     * @param \DateTime $fecha Fecha de los turnos requeridos
     * @param integer $limit Cantidad máxima de registros a retornar
     * @param integer $offset Cantidad de registros a saltar
     * @param string $codigosTurnos codigos de los turnos
     * @param callback $onError Callback para devolver respuesta fallida
     * @return mixed
     */
    public function findTurnosBySnc(
        $puntoAtencionId,
        $fecha,
        $offset,
        $limit,
        $codigosTurnos,
        $params,
        $onError
    ) {
        $result = [];
        $resultset = [];
        
        // validamos que los datos vengan firmados
        $validateResultado = $this->communicationValidator->validateSNCRequest($params);

        if (! $validateResultado->hasError()) {
            $validateResultado = $this->turnoValidator->validarBuscarTurno($puntoAtencionId, $fecha);

            if (! $validateResultado->hasError()) {
                $result = $this->turnoRepository->findTurnosBySnc(
                    $puntoAtencionId,
                    $fecha,
                    $offset,
                    $limit,
                    $codigosTurnos,
                    $params
                );
                $resultset = [
                    'resultset' => [
                        'count' => $this->turnoRepository->getTotalTurnos(
                            $puntoAtencionId,
                            $fecha,
                            $codigosTurnos,
                            $params
                        ),
                        'offset' => $offset,
                        'limit' => $limit
                    ]
                ];
            }
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
     * Cancelar turnos por fecha
     *
     * @param array $params arreglo con los datos (fecha, punto de atención y motivo)
     * @param callback $success Callback para devolver respuesta exitosa
     * @param callback $error Callback para devolver respuesta fallida
     * @return mixed
     */
    public function cancelByDate($params, $success, $error)
    {
        $turnoSync = new TurnoSync(
            $this->turnoRepository,
            new DatosTurnoFactory(),
            $this->turnoValidator,
            $this->notificationsService
        );

        $validateResult = $turnoSync->cancelByDate($params);
        $notificaciones = [];
        if (! $validateResult->hasError()) {
            $turnosBatch = array_chunk($validateResult->getEntity(), $this->notificationsService->getBatchLimit());
            $nuevoTurnoUrl = $this->notificationsService->getEmailUrl('nuevo');
            foreach ($turnosBatch as $turnos) {
                // agregamos los parámetros del mensaje
                $turnos = array_map(function ($turno) use ($params, $nuevoTurnoUrl) {
                    $turno['params'] = [
                        'nombre' => $turno['nombre'],
                        'apellido' => $turno['apellido'],
                        'lugar' => $turno['lugar'],
                        'direccion' => $turno['direccion'],
                        'fecha' => $turno['fecha']->format('d-m-Y'),
                        'hora' => $turno['hora']->format('H:i'),
                        'tramite' => $turno['tramite'],
                        'motivo' => $params['motivo'],
                        'nuevo_url' => sprintf($nuevoTurnoUrl, $turno['tramite_id'])
                    ];
                    return $turno;
                }, $turnos);
                // enviamos notificación
                try {
                    $notificaciones[] = $this->notificationsService->enviarNotificacionBatch(
                        $this->notificationsService->getEmailTemplate('turnocancelar'),
                        $turnos
                    );
                } catch (\Exception $e) {
                    $notificaciones[] = $e->getMessage();
                }
            }
            
            $this->redisServices->redisDelTurnoByPuntoDeAtencion($params['puntoatencion']);
            // agregamos el dia no habil al punto de atención
            $puntoAtencion = $this->puntoAtencionRepository->find($params['puntoatencion']);
            $diaNoLaborable = new DiaNoLaborable(new \DateTime($params['fecha']), $puntoAtencion);
            $this->diaNoLaborableRepository->add($diaNoLaborable);
        }

        return $this->processResult(
            $validateResult,
            function ($entities) use ($success, $notificaciones) {
                return call_user_func_array($success, [$this->turnoRepository->flush(), $notificaciones]);
            },
            $error
        );
    }

    /**
     * Obtener las fechas de reasignación por grupo de trámites
     * @param int $puntoAtencionId Identificador único del punto de atencion
     * @param array $params arreglo con los datos de fecha y grupo trámite
     * @param callback $onError Callback para devolver respuesta fallida
     * @return array
     */
    public function findTurnosReasignacion($puntoAtencionId, $params, $onError)
    {
        $resultset = [];
        $result = [
            'total_turnos' => 0,
            'grupo_tramites' => []
        ];
        // validamos que la fecha y el punto de atención sean validos
        $validateResultado = $this->turnoValidator->validateFechaReasignacion($params, $puntoAtencionId);

        if (! $validateResultado->hasError()) {
            // obtener los turnos a reasignar agrupados por grupo de trámites
            $turnos = $this->turnoRepository->findTurnosByPuntoAtencionFecha($puntoAtencionId, $params['fecha']);
            foreach ($turnos as $turno) {
                $result['total_turnos'] += (int) $turno['total_turnos'];
                $result['grupo_tramites'][] = [
                    'id' => $turno['id'],
                    'nombre' => $turno['nombre'],
                    'total_turnos' => (int) $turno['total_turnos']
                ];
            }
        }
        return $this->processError(
            $validateResultado,
            function () use ($resultset, $result) {
                return $this->respuestaData($resultset, $result);
            },
            $onError
        );
    }

    /**
     * Obtener fechas de posible reasignación por grupo de trámites
     * @param integer $puntoAtencionId Identificador único del punto de atencion
     * @param integer $grupoTramiteId Identificador único del grupo trámite
     * @param array $params arreglo con los datos
     * @param callback $onError Callback para devolver respuesta fallida
     * @return array
     */
    public function findFechasReasignacion($puntoAtencionId, $grupoTramiteId, $tramiteId, $params, $onError)
    {
        $resultset = [];
        $result = [
            'id' => null,
            'nombre' => null,
            'total_turnos' => 0,
            'fechas' => []
        ];
        // validamos que la fecha y el punto de atención sean validos
        $validateResultado = $this->turnoValidator->validateFechaReasignacion(
            $params,
            $puntoAtencionId,
            $grupoTramiteId
        );
        if (! $validateResultado->hasError()) {
            $grupoTramite = $this->grupoTramiteRepository->find($grupoTramiteId);
            $result['id'] = $grupoTramite->getId();
            $result['nombre'] = $grupoTramite->getNombre();
            $result['total_turnos'] = $this->turnoRepository->findTotalTurnosByPuntoAtencionFecha(
                $puntoAtencionId,
                $grupoTramiteId,
                $params['fecha']
            );
            // disponibilidad
            $disponibilidad = $this->getDisponibilidadPDAGDT($puntoAtencionId, $grupoTramiteId);
            // fechas
            $date = new \DateTime($params['fecha']);
            while (count($result['fechas']) < 5) {
                $nextDay = $date->add(new \DateInterval('P1D'));
                $availableDate = $this->checkAvailableDay($puntoAtencionId, $nextDay, $disponibilidad, $tramiteId);
                if ($availableDate) {
                    $result['fechas'][$availableDate['fecha']] = $this->getTotalTurnosDisponiblesFecha(
                        $puntoAtencionId,
                        $grupoTramiteId,
                        $availableDate['fecha'],
                        $availableDate['turnos']
                    );
                }
                $date = $nextDay;
            }
        }
        return $this->processError(
            $validateResultado,
            function () use ($resultset, $result) {
                return $this->respuestaData($resultset, $result);
            },
            $onError
        );
    }

    /**
     * Reasignar turnos por grupo de trámites
     * @param integer $puntoAtencionId Identificador único del punto de atencion
     * @param array $params arreglo con los datos
     * @param callback $onSuccess Callback para devolver respuesta exitosa
     * @param callback $onError Callback para devolver respuesta fallida
     * @return array
     */
    public function reasignarTurnos($puntoAtencionId, $params, $onSuccess, $onError)
    {
        $validateResultado = $this->turnoValidator->validateReasignacion($params, $puntoAtencionId);
        
        if (! $validateResultado->hasError()) {
            $turnosGT = $validateResultado->getEntity();
            
            // iteramos sobre los grupos de trámites
            foreach ($turnosGT as $turnosFechas) {
                foreach ($turnosFechas['fechas'] as $fecha => $cantidad) {
                    $fecha = new \DateTime($fecha);
                    while ($cantidad > 0) {
                        $turno = array_shift($turnosFechas['turnos']);
                        $datosTurno = $turno->getDatosTurno();
                        $puntoAtencion = $turno->getPuntoAtencion();
                        $provincia = $puntoAtencion->getProvincia();
                        $localidad = $puntoAtencion->getLocalidad();
                        $tramite = $turno->getTramite();
                        // url
                        $cancelarTurnoUrl = $this->notificationsService->getEmailUrl('cancelar');
                        $cancelarTurnoUrl .= 'codigo/' . $turno->getCodigo() . '/cuil/' . $datosTurno->getCuil();
                        // actualizar fecha del turno
                        $turno->setFecha($fecha);
                        // Enviamos la notificación al ciudadano
                        $campos = [
                            'email' => $datosTurno->getEmail(),
                            'cuil' => $datosTurno->getCuil(),
                            'nombre' => $datosTurno->getNombre(),
                            'apellido' => $datosTurno->getApellido(),
                            'direccion' => $provincia->getNombre() . ', ' . $localidad->getNombre() . ', ' . $puntoAtencion->getDireccion(),
                            'lugar' => $puntoAtencion->getNombre(),
                            'fecha' => $fecha->format('d-m-Y'),
                            'hora' => $turno->getHora()->format('H:i'),
                            'tramite' => $tramite->getNombre(),
                            'nuevo_url' => $cancelarTurnoUrl
                        ];


                        $reasignacion = new Reasignacion($campos);
                        $this->enviarEmailCiudadano($turno->getId(), ['campos' => $campos], "reasignacion");
                        $this->turnoRepository->add($reasignacion);
                        // disminuimos la cantidad de turnos a asignar por día
                        $cantidad--;
                    }
                }
            }
            $this->redisServices->redisDelTurnoByPuntoDeAtencion($puntoAtencionId);
        }

        // ver el call user func array para que haga, mande mail y siga con lo que tenga que hacer

        return $this->processResult(
            $validateResultado,
            function ($entity) use ($onSuccess) {
                return call_user_func($onSuccess, $this->turnoRepository->flush());
            },
            $onError
        );
    }

    /**
     * Obtener y dar formato a la disponibilidad por punto de atención y grupo de trámites
     * @param integer $puntoAtencionId Identificador único del punto de atencion
     * @param integer $grupoTramiteId Identificador único del grupo trámite
     * @return integer
     */
    private function getDisponibilidadPDAGDT($puntoAtencionId, $grupoTramiteId)
    {
        $disponibilidad = [];
        $dias = $this->disponibilidadRepository->getDisponibilidadByPuntoAtencionGrupoTramite(
            $puntoAtencionId,
            $grupoTramiteId
        );
        foreach ($dias as $dia) {
            $disponibilidad[$dia['diaSemana']] = $dia['cantidadTurnos'];
        }
        return $disponibilidad;
    }

    /**
     * Chequear si se puede reasignar turnos en este día
     * @param $puntoAtencionId
     * @param $date
     * @param $disponibilidad
     * @return mixed
     */
    private function checkAvailableDay($puntoAtencionId, $date, $disponibilidad, $tramiteId){
        // verificar si el siguiente día está dentro de la disponibilidad
        $dayNumber = (int) $date->format('N');
        if (array_key_exists($dayNumber, $disponibilidad)) {
            // verificamos si es un día laborable
            $isNotAvailable = $this->diaNoLaborableRepository->isDiaNoLaborable(
                $date->format('Y-m-d'),
                $puntoAtencionId
            );
            // verificamos si es un día NO laborable del tramite
            $isNotAvailableTramite = $this->diaNoLaborableTramiteRepository->isDiaNoLaborable(
                $date->format('Y-m-d'),
                $puntoAtencionId,
                $tramiteId
            );
            if (! $isNotAvailable && !$isNotAvailableTramite) {
                return [
                    'fecha' => $date->format('Y-m-d'),
                    'turnos' => $disponibilidad[$dayNumber]
                ];
            }
        }
        return null;
    }

    /**
     * Obtener el total de turnos disponibles por fecha
     * @param $puntoAtencionId
     * @param $grupoTramiteId
     * @param $fecha
     * @param $turnos
     * @return number
     */
    private function getTotalTurnosDisponiblesFecha($puntoAtencionId, $grupoTramiteId, $fecha, $turnos)
    {
        $turnosDados = $this->turnoRepository->findTotalTurnosByPuntoAtencionFecha(
            $puntoAtencionId,
            $grupoTramiteId,
            $fecha
        );
        $totalTurnos = $turnos - $turnosDados;
        return $totalTurnos < 0 ? 0 : $totalTurnos;
    }

    /**
     * Generamos los campos del turno que se van a devolver
     *
     * @param object $turno objeto de la entidad turno
     * @return mixed
     */
    private function getTurno(Turno $turno)
    {
        $response = [];
        $pda = $turno->getPuntoAtencion();
        $localidad = $pda->getLocalidad();
        $provincia = $localidad->getProvincia();

        $response['id'] = $turno->getId();
        $response['codigo'] = $turno->getCodigo();
        if ($turno->getEstado() !== Turno::ESTADO_CANCELADO) {
            $response['punto_atencion'] = [
                'id' => $pda->getId(),
                'nombre' => $pda->getNombre(),
                'direccion' => $pda->getDireccion(),
                'latitud' => $pda->getLatitud(),
                'longitud' => $pda->getLongitud(),
                'localidad' => $localidad->getNombre(),
                'provincia' => $provincia->getNombre()
            ];
            $response['alerta'] = $turno->getAlerta() ? $turno->getAlerta() : null;
            $response['fecha'] = $turno->getFecha() ? $turno->getFecha()->format('Y-m-d') : null;
            $response['hora'] = $turno->getHora() ? $turno->getHora()->format('H:i') : null;
            $response['fecha_creacion'] = $turno->getFechaCreado() ? $turno->getFechaCreado()->format('Y-m-d') : null;
            $response['tramite'] = [
                'id' => $turno->getTramite()->getId(),
                'nombre' => $turno->getTramite()->getNombre()
            ];
            $response['formulario'] = [
                'campos' => $turno->getTramite()->getFormulario() ? $turno->getTramite()->getFormulario()->getCampos():null
            ];
            $response['grupo_tramite'] = [
                'id' => $turno->getGrupoTramite()->getId()
            ];
            $response['datos_turno'] = [];

            if ($turno->getDatosTurno()) {
                $response['datos_turno']['nombre'] = $turno->getDatosTurno()->getNombre();
                $response['datos_turno']['apellido'] = $turno->getDatosTurno()->getApellido();
                $response['datos_turno']['cuil'] = $turno->getDatosTurno()->getCuil();
                $response['datos_turno']['email'] = $turno->getDatosTurno()->getEmail();
                $response['datos_turno']['nombre'] = $turno->getDatosTurno()->getNombre();
                $response['datos_turno']['apellido'] = $turno->getDatosTurno()->getApellido();
                $response['datos_turno']['telefono'] = $turno->getDatosTurno()->getTelefono();
                $response['datos_turno']['campos'] = FormHelper::camposFormulario(
                    $turno->getDatosTurno()->getCampos()
                );
            }
        }
        $response['estado'] = $turno->getEstado();
        $response['area'] = [
            'id' => $turno->getTramite()->getArea()[0]->getId(),
            'nombre' => $turno->getTramite()->getArea()[0]->getNombre(),
            'abreviatura' => $turno->getTramite()->getArea()[0]->getAbreviatura()
        ];
        if($turno->getUser()){
            $response['username'] = $turno->getUser()->getUsername();
        }
        $response['origen'] = $turno->getOrigen();
        return $response;
    }

    /**
     * Generamos el listado de turnos a devolver
     *
     * @param array $turnos arreglo de turnos
     * @return mixed
     */
    private function getTurnos($turnos)
    {
        $result = [];
        foreach ($turnos as $key => $turno) {
            $result[$key] = $this->getTurno($turno);
        }
        return $result;
    }

    /**
     * Modificar fecha y hora de un turno
     *
     * @param array $params Array con fecha y hora que modificara el turno
     * @param integer $id Identificador único del turno
     * @param callback $success Callback para devolver respuesta exitosa
     * @param callback $error Callback para devolver respuesta fallida
     * @return mixed
     */
    public function modificar($params, $success, $error)
    {
        $turnoSync = new TurnoSync(
            $this->turnoRepository,
            new DatosTurnoFactory(),
            $this->turnoValidator,
            $this->notificationsService
        );

        $validateResult = $turnoSync->modificar($params, $this->puntoAtencionRepository);
        $notification = null;
        if (! $validateResult->hasError()) {

            $turno = $this->turnoRepository->searchOneByCodigo($params['codigo']);

            $datosTurno = $turno->getDatosTurno();
            $puntoAtencion = $turno->getPuntoAtencion();
            $provincia = $puntoAtencion->getProvincia();
            $localidad = $puntoAtencion->getLocalidad();
            $tramite = $turno->getTramite();
            // url
            $cancelarTurnoUrl = $this->notificationsService->getEmailUrl('nuevo');
            $cancelarTurnoUrl .= 'codigo/' . $turno->getCodigo() . '/cuil/' . $datosTurno->getCuil();
            $fecha = $turno->getFecha();

            // Enviamos la notificación al ciudadano
        $campos = [
            'email' => $datosTurno->getEmail(),
            'cuil' => $datosTurno->getCuil(),
            'nombre' => $datosTurno->getNombre(),
            'apellido' => $datosTurno->getApellido(),
            'direccion' => $provincia->getNombre() . ', ' . $localidad->getNombre() . ', ' . $puntoAtencion->getDireccion(),
            'lugar' => $puntoAtencion->getNombre(),
            'fecha' => $fecha->format('d-m-Y'),
            'hora' => $turno->getHora()->format('H:i'),
            'tramite' => $tramite->getNombre(),
            'nuevo_url' => $cancelarTurnoUrl
        ];

            $notification = $this->enviarEmailCiudadano($turno->getId(), ['campos' => $campos], "reasignacion");
            $this->redisDelTurno($validateResult);

        }

        return $this->processResult(
            $validateResult,
            function ($entity) use ($success, $notification) {
                return call_user_func_array($success, [$this->tramiteRepository->flush(), $notification]);
            },
            $error
        );
    }
    

        

    private function redisDelTurno($validateResult) {
        if (! $validateResult->hasError()) {
            if($validateResult->getEntity()){
                $this->redisServices->delDispTurnoByTurno($validateResult->getEntity());
            }    
        }

    }
    
    
    
     /**
     * Crear un turno
     *
     * @param array $params Array con los datos para crear un turno
     * @param callback $success Callback para devolver respuesta exitosa
     * @param callback $error Callback para devolver respuesta fallida
     * @return mixed
     */
    public function createMultiple($params, $user, $success, $error)
    {
        $turnoFactory = new TurnoFactory(
            $this->puntoAtencionRepository,
            $this->tramiteRepository,
            $this->turnoValidator,
            $this->notificationsService
        );
        $validateResults = $turnoFactory->createMultiple($params, $user);
        
        return $this->processResultMultiple(
            $validateResults,
            function ($entity) use ($success) {
                return call_user_func($success, $entity);
            },
            $error
        );
    }
    
    
    /**
     * Valida el resutlado del proceso
     *
     * @param array $validateResult Objeto a validar
     * @param callback $onSucess Callback para devolver respuesta exitosa
     * @param callback $onError Callback para devolver respuesta fallida
     * @return mixed
     */
    protected function processResultMultiple($validateResultset, $onSucess, $onError)
    {
        if ($validateResultset->hasError()) {
                return call_user_func($onError, $validateResultset->getErrors());
        }
        foreach ($validateResultset->getEntity() as $validateResult) {
           if ($validateResult->hasError()) {
                return call_user_func($onError, $validateResult->getErrors());
            } else {
                $errors = $this->validate($validateResult->getEntity());
                if ($this->hasErrors($errors)) {
                    return call_user_func($onError, $errors);
                }
            }
        }
        foreach ($validateResultset->getEntity() as $validateResult) {
            $entities[] = $this->turnoRepository->save($validateResult->getEntity());
        }
        if(isset($entities)){
            return call_user_func($onSucess, $entities);
        }
    }
    
    
     /**
     * Modificar un turno
     *
     * @param array $params Array con los datos para modificara un turno
     * @param callback $success Callback para devolver respuesta exitosa
     * @param callback $error Callback para devolver respuesta fallida
     * @return mixed
     */
    public function editMultiple($params, $success, $error){
        $turnoSync = new TurnoSync(
            $this->turnoRepository,
            new DatosTurnoFactory(),
            $this->turnoValidator,
            $this->notificationsService
        );

        $validateResult = $turnoSync->editMultiple($params);
        $notification = null;
        if (! $validateResult->hasError()) {
            $notification = $this->enviarEmailCiudadanoMultiple($validateResult, $params, "turnos_multiples");
        }
        $this->redisDelTurnoResultset($validateResult);
        return $this->processResultMultiple(
            $validateResult,
            function ($entity) use ($success, $notification) {
                return call_user_func_array($success, [$entity, $notification]);
            },
            $error
        );
    }
    
    
    /**
     * Enviar mail de turno al ciudadano
     *
     * @param integer $id Identificador único del turno
     * @param array $params arreglo con los datos del turno
     * @return mixed
     */
    public function enviarEmailCiudadanoMultiple($validateResultset, $params, $template){
        $turnos = $validateResultset->getEntity();
        foreach ($turnos as $validateResult) {
            $turno = $validateResult->getEntity();
            
            if ($turno && $turno->getDatosTurno()) {
                $datosTurno = $turno->getDatosTurno();
                $codigo = ServicesHelper::obtenerCodigoSimple($turno->getCodigo());
                $cancelarTurnoUrl = $this->notificationsService->getEmailUrl('cancelar');
                $cancelarTurnoUrl .= 'codigo/' . $codigo . '/cuil/' . $datosTurno->getCuil();
                $tipoDocumento = $turno->getTramite()->getExcepcional() ? 'Documento extranjero' : 'CUIL';
                $campos = $datosTurno->getCampos();
                $turnoData[] = [
                    'nombre' => $campos['nombre'],
                    'apellido' => $campos['apellido'],
                    'tipo_documento' => $tipoDocumento,
                    'cuil' => $campos['cuil'],
                    'codigo' => $codigo,
                    'fecha' => $turno->getFecha()->format('d-m-Y'),
                    'horario' => $turno->getHora()->format('H:i'),
                    'tramite' => $turno->getTramite()->getNombre(),
                    'lugar' => $turno->getPuntoAtencion()->getNombre(),
                    'direccion' => $turno->getPuntoAtencion()->getDireccion() . '. ' .
                        $turno->getPuntoAtencion()->getProvincia()->getNombre() . ', ' .
                        $turno->getPuntoAtencion()->getLocalidad()->getNombre(),
                    'requisitos' => $this->parser->render($turno->getTramite()->getRequisitos()),
                    'email' => $campos['email'],
                    'cancelar_url' => $cancelarTurnoUrl,
                ];
            }
        }
        
        try {
            return $this->notificationsService->enviarNotificacion(
                $this->notificationsService->getEmailTemplate($template),
                $params['email'],
                $params['cuilSolicitante'],
                $turnoData
            );
        } catch (Exception $exception) {
            // nada
            return null;
        }
        return null;
    }
    
    private function redisDelTurnoResultset($validateResultset) {
        if (! $validateResultset->hasError()) {
            $turnos = $validateResultset->getEntity();
            foreach ($turnos as $validateResult) {
                if (! $validateResult->hasError()) {
                    if($validateResult->getEntity()){
                        $this->redisServices->delDispTurnoByTurno($validateResult->getEntity());
                    }    
                }
            }
        }
    }
}

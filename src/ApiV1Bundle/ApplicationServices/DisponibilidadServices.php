<?php
namespace ApiV1Bundle\ApplicationServices;

use ApiV1Bundle\Entity\Factory\DisponibilidadFactory;
use ApiV1Bundle\Entity\PuntoTramite;
use ApiV1Bundle\Entity\PuntoAtencion;
use ApiV1Bundle\Entity\Sync\DisponibilidadSync;
use ApiV1Bundle\Repository\DisponibilidadRepository;
use ApiV1Bundle\Repository\GrupoTramiteRepository;
use ApiV1Bundle\Repository\HorarioAtencionRepository;
use ApiV1Bundle\Repository\PuntoAtencionRepository;
use ApiV1Bundle\Repository\PuntoTramiteRepository;
use Symfony\Component\DependencyInjection\Container;
use ApiV1Bundle\Repository\TramiteRepository;
use ApiV1Bundle\Entity\Validator\DisponibilidadValidator;
use ApiV1Bundle\Entity\ValidateResultado;
use ApiV1Bundle\Repository\TurnoRepository;
use ApiV1Bundle\Repository\DiaNoLaborableTramiteRepository;
use Symfony\Component\Validator\Constraints\DateTime;
use ApiV1Bundle\ApplicationServices\RedisServices;
/**
 * Class DisponibilidadServices
 * @package ApiV1Bundle\ApplicationServices
 */
class DisponibilidadServices extends SNTServices
{
    /** @var TramiteRepository  */
    private $tramiteRepository;
    /** @var DisponibilidadValidator  */
    private $disponibilidadValidator;
    /** @var PuntoAtencionRepository  */
    private $puntoAtencionRepository;
     /** @var GrupoTramiteRepository  */
    private $grupoTramiteRepository;
    /** @var HorarioAtencionRepository  */
    private $horarioAtencionRepository;
    /** @var DisponibilidadRepository  */
    private $disponibilidadRepository;
    /** @var TurnoRepository  */
    private $turnoRepository;
    /** @var PuntoTramiteRepository  */
    private $puntoTramiteRepository;
    /** @var RedisServices  */
    private $redisServices;
    /** @var DiaNoLaborableTramiteRepository  */
    private $diaNoLaborableTramiteRepository;

    /**
     * DisponibilidadServices constructor.
     * @param Container $container
     * @param TramiteRepository $tramiteRepository
     * @param DisponibilidadValidator $disponibilidadValidator
     * @param PuntoAtencionRepository $puntoAtencionRepository
     * @param GrupoTramiteRepository $grupoTramiteRepository
     * @param HorarioAtencionRepository $horarioAtencionRepository
     * @param DisponibilidadRepository $disponibilidadRepository
     * @param TurnoRepository $turnoRepository
     * @param PuntoTramiteRepository $puntoTramiteRepository
     * @param RedisServices $redisServices
     * @param DiaNoLaborableTramiteRepository
     */
    public function __construct(
        Container $container,
        TramiteRepository $tramiteRepository,
        DisponibilidadValidator $disponibilidadValidator,
        PuntoAtencionRepository $puntoAtencionRepository,
        GrupoTramiteRepository $grupoTramiteRepository,
        HorarioAtencionRepository $horarioAtencionRepository,
        DisponibilidadRepository $disponibilidadRepository,
        TurnoRepository $turnoRepository,
        PuntoTramiteRepository $puntoTramiteRepository,
        RedisServices $redisServices,
        DiaNoLaborableTramiteRepository $diaNoLaborableTramiteRepository
    ) {
        parent::__construct($container);
        $this->tramiteRepository = $tramiteRepository;
        $this->disponibilidadValidator = $disponibilidadValidator;
        $this->puntoAtencionRepository = $puntoAtencionRepository;
        $this->grupoTramiteRepository = $grupoTramiteRepository;
        $this->horarioAtencionRepository = $horarioAtencionRepository;
        $this->disponibilidadRepository = $disponibilidadRepository;
        $this->turnoRepository = $turnoRepository;
        $this->puntoTramiteRepository = $puntoTramiteRepository;
        $this->redisServices = $redisServices;
        $this->diaNoLaborableTramiteRepository = $diaNoLaborableTramiteRepository;
    }

    /**
     * Permite obtener los puntos de atención por trámite + provincia + localidad + fecha (opcional)
     *
     * @param array $params array con datos para poder obtener el punto de atención solicitado
     * @param callback $onError Callback para devolver respuesta fallida
     * @return mixed
     */
    public function getPuntosAtencionDisponibles($params, $onError)
    {
        $resultPDADisponible = [];

        
        $validateResultado = $this->disponibilidadValidator->validarParamsToPuntoAtencion($params);

        if (! $validateResultado->hasError()) {

            $tramite = $this->tramiteRepository->find($params['tramiteId']);
            $puntosAtencion = $this->puntoAtencionRepository->findPuntoAtencionBy($params['provincia'], $params['localidad']);

            foreach ($puntosAtencion as $puntoAtencion) {
                if ($this->puntoAtencionRepository->checkTramiteRelationship($puntoAtencion->getId(), $tramite->getId())) {
                    if (! $puntoAtencion->getEstado()) {
                        continue;
                    }

                    /** @var PuntoTramite $puntoTramite */
                    $puntoTramite = $this->puntoTramiteRepository->findOneBy([
                        'tramite' => $tramite,
                        'puntoAtencion' => $puntoAtencion
                    ]);

                    if (! $puntoTramite->getEstado()) {
                        continue;
                    }

                    $resultPDADisponible[] = [
                       'id' => $puntoAtencion->getId(),
                       'nombre' => $puntoAtencion->getNombre(),
                       'provincia' => $puntoAtencion->getProvinciaNombre(),
                       'localidad' => $puntoAtencion->getLocalidadNombre(),
                       'direccion' => $puntoAtencion->getDireccion(),
                       'latitud' => null,
                       'longitud' => null,
                       'disponible' => count($this->puntoAtencionRepository->getDisponibilidadFecha($puntoAtencion, $tramite, null,  $params, $this->redisServices, $this->diaNoLaborableTramiteRepository)) > 0
                    ];
                }
            }
        }

        return $this->processError(
            $validateResultado,
            function () use ($resultPDADisponible) {
                return $this->respuestaData([], $resultPDADisponible);
            },
            $onError
        );
    }

    /**
     * Obtener las fechas disponibles
     *
     * @param array $params array con los datos para poder obtener las fechas disponibles.
     * @param callback $onError Callback para devolver respuesta fallida
     * @return mixed
     */
    public function getFechasDisponibles($params, $onError)
    {
        $validateResultado = $this->disponibilidadValidator->validarParamsToFechas($params);
        $resultFechasDisponible = [];

        if (! $validateResultado->hasError()) {
            $puntoAtencion = $this->puntoAtencionRepository->find($params['puntoAtencionId']);
            $tramite = $this->tramiteRepository->find($params['tramiteId']);
            $puntoTramite = $this->puntoTramiteRepository->findOneBy([
                'puntoAtencion' => $puntoAtencion->getId(),
                'tramite' => $tramite->getId()
            ]);

            $resultFechasDisponible = $this->puntoAtencionRepository->getDisponibilidadFecha($puntoAtencion, $tramite, $puntoTramite, $params, $this->redisServices, $this->diaNoLaborableTramiteRepository );
        }

        return $this->processError(
            $validateResultado,
            function () use ($resultFechasDisponible) {
                return $this->respuestaData([], $resultFechasDisponible);
            },
            $onError
        );
    }

    /**
     * Obtener los horarios disponibles de un punto de atención
     *
     * @param array $params array con los datos para obtener los horarios.
     * @param integer $puntoAtencionId identificador único del punto de atención
     * @param callback $onError Callback para devolver respuesta fallida
     * @return mixed
     */
    public function getHorariosDisponibles($params, $puntoAtencionId, $onError)
    {
        $validateResultado = $this->disponibilidadValidator->validarParamsToHorarios($puntoAtencionId, $params);
        $resultHorariosDisponible = [];
        $metadata = [
            'punto_atencion' => $puntoAtencionId
        ];

        if (! $validateResultado->hasError()) {
            $fecha = new \DateTime($params['fecha']);

            $tramite = $this->tramiteRepository->find($params['tramiteId']);
            $puntoAtencion = $this->puntoAtencionRepository->find($puntoAtencionId);
            $grupoTramite = $tramite->getGrupoTramiteByPunto($puntoAtencion);

            $horarios = $this->horarioAtencionRepository->getDisponibilidadFechaHora(
                 $puntoAtencionId,
                 $grupoTramite->getId(),
                 $fecha->format('Y-m-d')
             );

            $horariosDisponibilidad = $this->horarioAtencionRepository->getDisponibilidadHora(
                $puntoAtencionId,
                $grupoTramite->getId(),
                $fecha->format('Y-m-d')
            );

            $totalTurnos = 0;
            $count = 0;
            $coincide = false;
            foreach ($horarios as $horario) {

                $fecha_actual = strtotime(date('Y-m-d')); 
                $fecha_comparar = strtotime($horario['fecha']);
                
                $hora_actual  = strtotime(date('H:i:s'), time());
                $hora_comparar  = strtotime($horario['horario'], time());

                foreach ($horariosDisponibilidad as $horaDisponibilidad) {
                    
                    if ($horario['horario'] == $horaDisponibilidad['hora']){
                        $coincide = true;

                        if($fecha_actual == $fecha_comparar && $hora_actual < $hora_comparar){
                            $resultHorariosDisponible[] = [
                                'cantidadTurnos' => $horario['cantidad_turnos'] - $horaDisponibilidad['turnos_dados'],
                                'fecha' => $horario['fecha'],
                                'horario' => $horario['horario'],
                            ];
                            $totalTurnos +=  $horario['cantidad_turnos'] - $horaDisponibilidad['turnos_dados'];
                        }
                        elseif($fecha_actual != $fecha_comparar){
                            $resultHorariosDisponible[] = [
                                'cantidadTurnos' => $horario['cantidad_turnos'] - $horaDisponibilidad['turnos_dados'],
                                'fecha' => $horario['fecha'],
                                'horario' => $horario['horario'],
                            ];
                            $totalTurnos +=  $horario['cantidad_turnos'] - $horaDisponibilidad['turnos_dados'];
                        }
                    }
                }
                
                if (! $coincide) {
                    if($fecha_actual == $fecha_comparar && $hora_actual < $hora_comparar){
                        $resultHorariosDisponible[] = [
                            'cantidadTurnos' => (int) $horario['cantidad_turnos'],
                            'fecha' => $horario['fecha'],
                            'horario' => $horario['horario'],
                        ];
                        $totalTurnos +=  $horario['cantidad_turnos'];
                    }
                    elseif($fecha_actual != $fecha_comparar){
                        $resultHorariosDisponible[] = [
                            'cantidadTurnos' => (int) $horario['cantidad_turnos'],
                            'fecha' => $horario['fecha'],
                            'horario' => $horario['horario'],
                        ];
                        $totalTurnos +=  $horario['cantidad_turnos'];
                    }
                }
            
                $coincide = false;
            }

             $metadata = [
                 'puntoAtencion' => (int) $puntoAtencionId,
                 'totalHorarios' => count($resultHorariosDisponible),
                 'totalTurnos' => $totalTurnos,
                 'fecha' => $fecha->format(\DateTime::RFC2822)
             ];
         }


         return $this->processError(
             $validateResultado,
             function () use ($metadata, $resultHorariosDisponible) {
                 return $this->respuestaData($metadata, $resultHorariosDisponible);
             },
             $onError
         );
     }

    /**
     * Devuelve true/false si hay disponiblidad en una fecha y hora seleccionada
     *
     * @param integer $grupoTramiteId identificador único del grupo trámite
     * @param integer $puntoAtencionId identificador único del punto de atención
     * @param date $fecha fecha a buscar
     * @param time $hora hora a buscar
     * @return bool
     */
     public function hasTurnoDisponible($grupoTramiteId, $puntoAtencionId, $fecha, $hora)
     {

         $turnosDados = $this->turnoRepository->findTurnosDados($puntoAtencionId, $grupoTramiteId, $fecha, $hora);
         $horarios = $this->horarioAtencionRepository->getDisponibilidadFechaHora(
             $puntoAtencionId,
             $grupoTramiteId,
             $fecha
         );

         foreach ($horarios as $horario) {
             if ($horario['horario'] == $hora) {
                 return ($horario['cantidad_turnos'] - $turnosDados > 0) ;
             }
         }
         return false;
     }

    /**
     * Crear nueva disponiblidad por punto de atención, grupo de tramite y horario de atención
     *
     * @param array $params Array con los datos para crear una nueva disponibilidad
     * @param callback $success Callback para devolver respuesta exitosa
     * @param callback $error Callback para devolver respuesta fallida
     * @return mixed
     */
    public function create($params, $success, $error)
    {
        $disponibilidadFactory = new DisponibilidadFactory(
            $this->puntoAtencionRepository,
            $this->grupoTramiteRepository,
            $this->horarioAtencionRepository,
            $this->disponibilidadValidator
        );

        $validateResult = $disponibilidadFactory->create($params);

        return $this->processResult(
            $validateResult,
            function ($entity) use ($success) {
                return call_user_func($success, $this->disponibilidadRepository->save($entity));
            },
            $error
        );
    }

    /**
     * Edita un la disponibilidad
     *
     * @param array $params Array con los parámetros
     * @param integer $idRow Identificador de la fila del Horario de Atencion
     * @param callback $sucess Callback para devolver respuesta exitosa
     * @param callback $error Callback para devolver respuesta fallida
     * @return mixed
     */
    public function edit($params, $idRow, $sucess, $error)
    {
        $disponibilidadSync = new DisponibilidadSync(
            $this->disponibilidadValidator,
            $this->disponibilidadRepository,
            $this->puntoAtencionRepository,
            $this->grupoTramiteRepository,
            $this->horarioAtencionRepository
        );

        $validateResult = $disponibilidadSync->edit($idRow, $params);

        return $this->processError(
            $validateResult,
            function () use ($sucess) {
                return call_user_func($sucess, $this->disponibilidadRepository->flush());
            },
            $error
        );
    }

    /**
     * Obtener la cantidad de turnos de un Punto de Atencion por grupo de trámite y agrupado por  rango horario
     *
     * @param integer $puntoAtencionId identificador único del punto de atención
     * @param integer $grupoTramiteId identificador único del grupo trámite
     * @param callback $onError Callback para devolver respuesta fallida
     * @return mixed
     */
    public function getAllTurnosByPuntoAtencionGrupoTramite($puntoAtencionId, $grupoTramiteId, $onError)
    {
        $grupoTramite = $this->grupoTramiteRepository->find($grupoTramiteId);
        $puntoAtencion = $this->puntoAtencionRepository->find($puntoAtencionId);
        $validateResultado = $this->disponibilidadValidator->validarParamsGet($puntoAtencion, $grupoTramite);

        if (! $validateResultado->hasError()) {
            $disponibilidadSync = new DisponibilidadSync(
                $this->disponibilidadValidator,
                $this->disponibilidadRepository,
                $this->puntoAtencionRepository,
                $this->grupoTramiteRepository,
                $this->horarioAtencionRepository
            );

            $listaCapacidadTurnos = $disponibilidadSync->listarCapacidadAgrupadaByHorario(
                $puntoAtencion,
                $grupoTramite
            );
            return $this->respuestaData([], $listaCapacidadTurnos);
        }

        return call_user_func($onError, $validateResultado->getErrors());
    }

    /**
     * Chequea si existe el punto de atención para el trámite
     * @param integer $tramiteId identificador único del trámite
     * @param integer $puntoAtencionId identificador único del punto de atención
     * * @return mixed
    */
    public function existeTramitePuntoAtencion($tramiteId,$puntoAtencionId){

        $puntoTramite = $this->puntoTramiteRepository->findOneBy([
            'tramite' => $tramiteId,
            'puntoAtencion' => $puntoAtencionId
        ]);

        if(!$puntoTramite){
            return false;
        }
        return true;
    }
    
    
     /**
     * Obtener tramites y sus fechas disponibles
     *
     * @param array $params array con los datos para poder obtener las fechas disponibles.
     * @param callback $onError Callback para devolver respuesta fallida
     * @return mixed
     */
    public function getTramitesFechasDisponibles($params, $onError)
    {
        $validateResultado = $this->disponibilidadValidator->validarParamsDisponibilidadTramites($params);
        $resultFechasDisponible = [];

        if (! $validateResultado->hasError()) {
            $tramite = $this->tramiteRepository->find($params['tramiteId']);
            $resultFechasDisponible = $this->puntoAtencionRepository->getDisponibilidadFechaTramites( $tramite, $params, $this->redisServices);
        }

        return $this->processError(
            $validateResultado,
            function () use ($resultFechasDisponible) {
                return $this->respuestaData([], $resultFechasDisponible);
            },
            $onError
        );
    }

}

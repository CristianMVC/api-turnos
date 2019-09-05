<?php
/**
 * Class HorarioAtencionServices
 * @package ApiV1Bundle\ApplicationServices
 */

namespace ApiV1Bundle\ApplicationServices;

use ApiV1Bundle\Entity\HorarioAtencion;
use ApiV1Bundle\Repository\DisponibilidadRepository;
use ApiV1Bundle\Repository\GrupoTramiteRepository;
use Symfony\Component\DependencyInjection\Container;
use ApiV1Bundle\Entity\Factory\HorarioAtencionFactory;
use ApiV1Bundle\Entity\Sync\HorarioAtencionSync;
use ApiV1Bundle\Repository\PuntoAtencionRepository;
use ApiV1Bundle\Repository\HorarioAtencionRepository;
use ApiV1Bundle\Entity\Validator\HorarioAtencionValidator;
use ApiV1Bundle\Entity\ValidateResultado;
use ApiV1Bundle\Helper\ServicesHelper;
use ApiV1Bundle\ApplicationServices\RedisServices;

/**
 * Class HorarioAtencionServices
 * @package ApiV1Bundle\ApplicationServices
 */
class HorarioAtencionServices extends SNTServices
{
    /** @var PuntoAtencionRepository  */
    private $puntoAtencionRepository;
    /** @var HorarioAtencionRepository  */
    private $horarioAtencionRepository;
    /** @var HorarioAtencionValidator  */
    private $horarioAtencionValidator;
    /** @var DisponibilidadRepository  */
    private $disponibilidadRepository;
    /** @var GrupoTramiteRepository  */
    private $grupoTramiteRepository;
    /** @var RedisServices  */
    private $redisServices;

    /**
     * HorarioAtencionServices constructor.
     * @param Container $container
     * @param PuntoAtencionRepository $puntoAtencionRepository
     * @param HorarioAtencionRepository $horarioAtencionRepository
     * @param HorarioAtencionValidator $horarioAtencionValidator
     * @param DisponibilidadRepository $disponibilidadRepository
     * @param GrupoTramiteRepository $grupoTramiteRepository
     * @param RedisServices $redisServices
     */
    public function __construct(
        Container $container,
        PuntoAtencionRepository $puntoAtencionRepository,
        HorarioAtencionRepository $horarioAtencionRepository,
        HorarioAtencionValidator $horarioAtencionValidator,
        DisponibilidadRepository $disponibilidadRepository,
        GrupoTramiteRepository $grupoTramiteRepository,
        RedisServices $redisServices
    ) {
        parent::__construct($container);
        $this->puntoAtencionRepository = $puntoAtencionRepository;
        $this->horarioAtencionRepository = $horarioAtencionRepository;
        $this->horarioAtencionValidator = $horarioAtencionValidator;
        $this->disponibilidadRepository = $disponibilidadRepository;
        $this->grupoTramiteRepository = $grupoTramiteRepository;
        $this->redisServices = $redisServices;
    }

    /**
     * Listar Horarios de atención por punto de atención
     *
     * @param integer $puntoAtencionId Identificador único del punto de atención
     * @param integer $limit Cantidad máxima de registros a retornar
     * @param integer $offset Cantidad de registros a saltar
     * @return mixed
     */
    public function findAllPaginate($puntoAtencionId, $limit, $offset)
    {
        $result = array_map(
            [$this, 'parseDates'],
            $this->horarioAtencionRepository->findAllPaginate($puntoAtencionId, $offset, $limit)
        );

        $resultset = [
            'resultset' => [
                'count' => $this->horarioAtencionRepository->getTotal($puntoAtencionId),
                'offset' => $offset,
                'limit' => $limit
            ]
        ];
        return $this->respuestaData($resultset, $result);
    }

    /**
     * obtener Horario de un punto de atención
     *
     * @param integer $puntoAtencionId Identificador único del punto de atención
     * @param integer $id Identificador único de horario de punto de atención
     * @return mixed
     */
    public function get($puntoAtencionId, $id)
    {
        $horarioAtencion = $this->horarioAtencionRepository->find($id);
        if ($horarioAtencion && $horarioAtencion->getPuntoAtencion()->getId() == $puntoAtencionId) {
            return $this->respuestaData([], [
                'id' => $horarioAtencion->getId(),
                'diasSemana' => $horarioAtencion->getDiaSemana(),
                'horaInicio' => $horarioAtencion->getHoraInicio()->format('H:i'),
                'horaFin' => $horarioAtencion->getHoraFin()->format('H:i')
            ]);
        }
        return $this->respuestaData([], null);
    }

    /**
     * Crear un horario de atencion
     *
     * @param array $params Array con los datos para crear un horario de atención
     * @param integer $puntoAtencionId Identificador único del punto de atención
     * @param callback $success Callback para devolver respuesta exitosa
     * @param callback $error Callback para devolver respuesta fallida
     * @return mixed
     */
    public function create($params, $puntoAtencionId, $success, $error)
    {
        $horarioAtencionFactory = new HorarioAtencionFactory(
            $this->puntoAtencionRepository,
            $this->horarioAtencionRepository,
            $this->horarioAtencionValidator,
            $this->disponibilidadRepository,
            $this->grupoTramiteRepository
        );
        $validateResult = $horarioAtencionFactory->create($params, $puntoAtencionId);
        
        $this->redisServices->redisDelDispByPuntoDeAtencion($puntoAtencionId);
        
        return $this->processResult(
            $validateResult,
            function () use ($success, $validateResult) {
                $this->horarioAtencionRepository->flush();
                return call_user_func($success, $validateResult->getEntity());
            },
            $error
        );
    }

    /**
     * Modificar horario de atención
     *
     * @param array $params Array con los datos para crear un horario de atención
     * @param integer $puntoAtencionId Identificador único del punto de atención
     * @param integer $idRow Identificador de la fila del horario del punto de atención
     * @param callback $success Callback para devolver respuesta exitosa
     * @param callback $error Callback para devolver respuesta fallida
     * @return mixed
     */
    public function edit($params, $puntoAtencionId, $idRow, $success, $error)
    {
        $horarioAtencionSync = new HorarioAtencionSync(
            $this->puntoAtencionRepository,
            $this->horarioAtencionRepository,
            $this->horarioAtencionValidator,
            $this->disponibilidadRepository,
            $this->grupoTramiteRepository
        );
        $validateResult = $horarioAtencionSync->edit($params, $puntoAtencionId, $idRow);
        
        $this->redisServices->redisDelDispByPuntoDeAtencion($puntoAtencionId);

        return $this->processResult(
            $validateResult,
            function () use ($success) {
                return call_user_func($success, $this->horarioAtencionRepository->flush());
            },
            $error
        );
    }

    /**
     * Eliminar horario de atención
     *
     * @param integer $puntoAtencionId ID del punto de atención
     * @param integer $idRow ID del horario del punto de atención
     * @param callback $success Callback para devolver respuesta exitosa
     * @param callback $error Callback para devolver respuesta fallida
     * @return mixed
     */
    public function delete($puntoAtencionId, $idRow, $success, $error)
    {
        $horarioAtencionSync = new HorarioAtencionSync(
            $this->puntoAtencionRepository,
            $this->horarioAtencionRepository,
            $this->horarioAtencionValidator,
            $this->disponibilidadRepository,
            $this->grupoTramiteRepository
        );
        $validateResult = $horarioAtencionSync->delete($puntoAtencionId, $idRow);
        
        $this->redisServices->redisDelDispByPuntoDeAtencion($puntoAtencionId);
        
        return $this->processError(
            $validateResult,
            function () use ($success) {
                return call_user_func($success, $this->horarioAtencionRepository->flush());
            },
            $error
        );
    }

    /**
     * Obtener los Horarios de atencion del punto de atención, agrupados por rowId
     *
     * @param integer $puntoAtencionId Identificador único del punto de atención
     * @return mixed
     */
    public function findAll($puntoAtencionId)
    {
        $puntoAtencion = $this->puntoAtencionRepository->find($puntoAtencionId);

        $validateResultado  = $this->horarioAtencionValidator->validarPuntoAtencion($puntoAtencion);
        if (! $validateResultado->hasError()) {
            $horarioAtencionSync = new HorarioAtencionSync(
                $this->puntoAtencionRepository,
                $this->horarioAtencionRepository,
                $this->horarioAtencionValidator,
                $this->disponibilidadRepository,
                $this->grupoTramiteRepository
            );

            $listaHorariosAtencion = $horarioAtencionSync->listarHorariosAtencionAgrupados($puntoAtencion);
            $total = count($listaHorariosAtencion);
            $resultset = [
                'count' => $total,
                'offset' => 0,
                'limit' => $total
            ];
            return $this->respuestaData($resultset, $listaHorariosAtencion);
        }
        return new ValidateResultado([], $validateResultado->getErrors());
    }

    /**
     * Retorna los horarios por Punto de Atencion y el IdRow
     *
     * @param integer $puntoAtencionId ID del punto de atención
     * @param integer $idRow ID del horario del punto de atención
     * @param callback $onError Callback para devolver respuesta fallida
     * @return mixed
     */
    public function getHorarioAtencionByIdRow($puntoAtencionId, $idRow, $onError)
    {
        $puntoAtencion = $this->puntoAtencionRepository->find($puntoAtencionId);
        $validateResultado  = $this->horarioAtencionValidator->validarPuntoAtencion($puntoAtencion);
        $listaHorariosAtencion = [];

        if (! $validateResultado->hasError()) {
            $horarioAtencionSync = new HorarioAtencionSync(
                $this->puntoAtencionRepository,
                $this->horarioAtencionRepository,
                $this->horarioAtencionValidator,
                $this->disponibilidadRepository,
                $this->grupoTramiteRepository
            );
            $horarioAtencion = $this->horarioAtencionRepository->findBy([
                'idRow' => $idRow,
                'puntoAtencion' => $puntoAtencionId
            ]);
            $listaHorariosAtencion = $horarioAtencionSync->listarHorariosAtencionAgrupadosByidRow(
                $horarioAtencion,
                $idRow
            );
        }

        return $this->processError(
            $validateResultado,
            function () use ($listaHorariosAtencion) {
                return $this->respuestaData([], $listaHorariosAtencion);
            },
            $onError
        );
    }

    /**
     * Dado un array con datos de hora inicio y hora fin los devuelve en formato hora.
     *
     * @param array $arr Array con las horas de inicio y fin
     * @return mixed
     */
    private function parseDates(&$arr)
    {
        $arr['horaInicio'] = $arr['horaInicio']->format('H:i');
        $arr['horaFin'] = $arr['horaFin']->format('H:i');
        return $arr;
    }

    /**
     * Rango horario de atención
     *
     * @param integer $puntoAtencionId ID del punto de atención
     * @param callback $error Callback para devolver respuesta fallida
     * @return mixed
     */
    public function getHoursAvailable($puntoAtencionId, $error)
    {
        $puntoAtencion = $this->puntoAtencionRepository->find($puntoAtencionId);
        $validateResultado = $this->horarioAtencionValidator->validarPuntoAtencion($puntoAtencion);

        if (!$validateResultado->hasError()) {
            $horarios = $this->horarioAtencionRepository->getHorariosGroupByRowId($puntoAtencionId);

            $horarioAtencionSync = new HorarioAtencionSync(
                $this->puntoAtencionRepository,
                $this->horarioAtencionRepository,
                $this->horarioAtencionValidator,
                $this->disponibilidadRepository,
                $this->grupoTramiteRepository
            );

            $remainder = $horarioAtencionSync->getIntervaloMaximo($horarios);

            $intervalos = [10, 15, 30, 60];
            switch ($remainder) {
                case 30:
                    $intervalos = [10, 15, 30];
                    break;
                case 15:
                case 45:
                    $intervalos = [15];
                    break;
            }

            return $this->respuestaData([], $intervalos);
        }

        return $error($validateResultado->getErrors());
    }

    /**
     * Listado de horarios
     *
     * @return mixed
     */
    public function getListHours()
    {
        return $this->respuestaData([], ServicesHelper::listadoHorario());
    }
}

<?php
namespace ApiV1Bundle\ApplicationServices;

use ApiV1Bundle\Entity\GrupoTramite;
use ApiV1Bundle\Entity\ValidateResultado;
use ApiV1Bundle\Repository\TurnoRepository;
use Symfony\Component\DependencyInjection\Container;
use ApiV1Bundle\Repository\TramiteRepository;
use ApiV1Bundle\Repository\GrupoTramiteRepository;
use ApiV1Bundle\Entity\Sync\GrupoTramitesSync;
use ApiV1Bundle\Entity\Factory\GrupoTramitesFactory;
use ApiV1Bundle\Repository\PuntoAtencionRepository;
use ApiV1Bundle\Entity\Validator\GrupoTramitesValidator;
use ApiV1Bundle\Helper\ServicesHelper;
use ApiV1Bundle\Repository\DisponibilidadRepository;
use ApiV1Bundle\ExternalServices\GrupoTramitesIntegration;
use ApiV1Bundle\ApplicationServices\RedisServices;
/**
 * Class GrupoTramitesServices
 * @package ApiV1Bundle\ApplicationServices
 */

class GrupoTramitesServices extends SNTServices
{
    /** @var Container */
    private $container;
    /** @var PuntoAtencionRepository  */
    private $puntoAtencionRepository;
    /** @var TramiteRepository  */
    private $tramiteRepository;
    /** @var GrupoTramiteRepository  */
    private $grupoTramitesRepository;
    /** @var GrupoTramitesValidator  */
    private $grupoTramitesValidator;
    /** @var DisponibilidadRepository  */
    private $disponibilidadRepository;
    /** @var GrupoTramitesIntegration  */
    private $integrationService;
    /** @var TurnoRepository  */
    private $turnoRepository;
    /** @var RedisServices  */
    private $redisServices;

    /**
     * GrupoTramitesServices constructor.
     *
     * @param Container $container
     * @param PuntoAtencionRepository $puntoAtencionRepository
     * @param TramiteRepository $tramiteRepository
     * @param GrupoTramiteRepository $grupoTramitesRepository
     * @param GrupoTramitesValidator $grupoTramitesValidator
     * @param DisponibilidadRepository $disponibilidadRepository
     * @param GrupoTramitesIntegration $integrationService
     * @param TurnoRepository $turnoRepository
     * @param RedisServices $redisServices
     */
    public function __construct(
        Container $container,
        PuntoAtencionRepository $puntoAtencionRepository,
        TramiteRepository $tramiteRepository,
        GrupoTramiteRepository $grupoTramitesRepository,
        GrupoTramitesValidator $grupoTramitesValidator,
        DisponibilidadRepository $disponibilidadRepository,
        GrupoTramitesIntegration $integrationService,
        TurnoRepository $turnoRepository,
        RedisServices $redisServices
    ) {
        parent::__construct($container);
        $this->puntoAtencionRepository = $puntoAtencionRepository;
        $this->tramiteRepository = $tramiteRepository;
        $this->grupoTramitesRepository = $grupoTramitesRepository;
        $this->grupoTramitesValidator = $grupoTramitesValidator;
        $this->disponibilidadRepository = $disponibilidadRepository;
        $this->integrationService = $integrationService;
        $this->turnoRepository = $turnoRepository;
        $this->redisServices = $redisServices;
    }

    /**
     * Grupos de tramites por punto de atención
     *
     * @param integer $puntoAtencionId Identificador único del punto de atención
     * @param integer $limit Cantidad máxima de registros a retornar
     * @param integer $offset Cantidad de registros a saltar
     * @return mixed
     */
    public function findAllPaginate($puntoAtencionId, $limit, $offset)
    {
        $result = $this->grupoTramitesRepository->findAllPaginate($puntoAtencionId, $limit, $offset);
        $resultGruposTramites = array_map(function ($grupo) {
            $grupo['intervalo'] = ServicesHelper::transformaFracionHoraria($grupo['intervalo']);
            $grupo['tramites'] = $this->tramiteRepository->findTramitesByGrupo($grupo['id']);
            return $grupo;
        }, $result);

        $resultset = [
            'resultset' => [
                'count' => $this->grupoTramitesRepository->getTotal($puntoAtencionId),
                'offset' => $offset,
                'limit' => $limit
            ]
        ];
        return $this->respuestaData($resultset, $resultGruposTramites);
    }

    /**
     * Obtener un grupo de tramites
     *
     * @param integer $puntoAtencionId Identificador único del punto de atención
     * @param integer $id Identificador único de grupo de trámite
     * @param callback $success Callback para devolver respuesta exitosa
     * @param callback $error Callback para devolver respuesta fallida
     * @return mixed
     */
    public function get($puntoAtencionId, $id, $success, $error)
    {
        $result = [];
        $grupoTramites = $this->grupoTramitesRepository->find($id);
        $validateResultado = $this->grupoTramitesValidator->validarEntidad($grupoTramites, "El grupo de trámite no existe");

        if (!$validateResultado->hasError()) {
            if ($grupoTramites->getPuntoAtencion()->getId() == $puntoAtencionId) {
                $result = [
                    'id' => $grupoTramites->getId(),
                    'nombre' => $grupoTramites->getNombre(),
                    'horizonte' => $grupoTramites->getHorizonte(),
                    'tramites' => $this->tramiteRepository->findTramitesByGrupo($id),
                    'intervalo' => ServicesHelper::transformaFracionHoraria($grupoTramites->getIntervaloTiempo())
                ];
            } else {
                $validateResultado = new ValidateResultado(null, ["El grupo de trámite no pertenece al punto de atención"]);
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
     * Crear grupo de tramites
     *
     * @param integer $puntoAtencionId Identificador único del punto de atención
     * @param array $params Array con los datos del grupo de trámites
     * @param callback $success Callback para devolver respuesta exitosa
     * @param callback $error Callback para devolver respuesta fallida
     * @return mixed
     */
    public function create($params, $puntoAtencionId, $success, $error)
    {
        $grupoTramite = null;
        $grupoTramitesFactory = new GrupoTramitesFactory(
            $this->puntoAtencionRepository,
            $this->grupoTramitesRepository,
            $this->tramiteRepository,
            $this->grupoTramitesValidator,
            $this->disponibilidadRepository
        );
        // transacción
        $this->grupoTramitesRepository->beginTransaction();
        $validateResult = $grupoTramitesFactory->create($params, $puntoAtencionId);
        if (! $validateResult->hasError()) {
            $grupoTramite = $validateResult->getEntity();
            $this->grupoTramitesRepository->save($grupoTramite);
            $validateResult = $this->integrationService->agregarCola($grupoTramite, $puntoAtencionId);
            if ($validateResult->hasError()) {
                $this->grupoTramitesRepository->rollback();
            }else {
                $this->grupoTramitesRepository->commit();
            }
        }
        $this->redisServices->redisDelDispByPuntoDeAtencion($puntoAtencionId);
        return $this->processResult(
            $validateResult,
            function () use ($success, $grupoTramite) {
                return call_user_func($success, $grupoTramite);
            },
            $error
        );
    }

    /**
     * Modificar grupo de trámites
     *
     * @param array $params Array con los datos para modificar un grupo de trámites
     * @param integer $puntoAtencionId Identificador único del punto de atención
     * @param integer $id Identificador único del grupo de trámites
     * @param callback $success Callback para devolver respuesta exitosa
     * @param callback $error Callback para devolver respuesta fallida
     * @return mixed
     */
    public function edit($params, $puntoAtencionId, $id, $success, $error)
    {
        $grupoTramitesSync = new GrupoTramitesSync(
            $this->puntoAtencionRepository,
            $this->tramiteRepository,
            $this->grupoTramitesRepository,
            $this->grupoTramitesValidator,
            $this->disponibilidadRepository,
            $this->turnoRepository
        );
        // transacción
        $this->grupoTramitesRepository->beginTransaction();
        $validateResult = $grupoTramitesSync->edit($params, $puntoAtencionId, $id);
        if (! $validateResult->hasError()) {
            $validateResult = $this->integrationService->editarCola($id, $params['nombre']);
            if ($validateResult->hasError()) {
                $this->grupoTramitesRepository->rollback();
            }else {
                $this->grupoTramitesRepository->commit();
            }
        }
        $this->redisServices->redisDelDispByPuntoDeAtencion($puntoAtencionId);
        return $this->processResult(
            $validateResult,
            function () use ($success, $validateResult) {
                $this->grupoTramitesRepository->flush();
                return call_user_func($success, $validateResult->getEntity());
            },
            $error
        );
    }

    /**
     * Eliminar grupo de tramites
     *
     * @param integer $puntoAtencionId Identificador único del punto de atención
     * @param integer $id Identificador de un grupo de trámites
     * @param callback $success Callback para devolver respuesta exitosa
     * @param callback $error Callback para devolver respuesta fallida
     * @return mixed
     */
    public function delete($puntoAtencionId, $id, $success, $error)
    {
        $grupoTramitesSync = new GrupoTramitesSync(
            $this->puntoAtencionRepository,
            $this->tramiteRepository,
            $this->grupoTramitesRepository,
            $this->grupoTramitesValidator,
            $this->disponibilidadRepository,
            $this->turnoRepository
        );
        // transacción
        $this->grupoTramitesRepository->beginTransaction();
        $validateResult = $grupoTramitesSync->delete($puntoAtencionId, $id);
        $result = null;
        if (! $validateResult->hasError()) {
            $this->grupoTramitesRepository->remove($validateResult->getEntity());
            $validateResult = $this->integrationService->eliminarCola($id);
            if ($validateResult->hasError()) {
                $this->grupoTramitesRepository->rollback();
            }else {
                $this->grupoTramitesRepository->commit();
            }
        }

        $this->redisServices->redisDelDispByPuntoDeAtencion($puntoAtencionId);
        return $this->processResult(
            $validateResult,
            function () use ($success,$validateResult) {
                $this->grupoTramitesRepository->flush();
                return call_user_func($success, $validateResult->getEntity());
            },
            $error
        );
    }

    /**
     * Asignar tramites al grupo de tramites
     *
     * @param array $params Array con los datos para modificar un grupo de trámites
     * @param integer $puntoAtencionId Identificador único del punto de atención
     * @param integer $id Identificador único del grupo de trámites
     * @param callback $success Callback para devolver respuesta exitosa
     * @param callback $error Callback para devolver respuesta fallida
     * @return mixed
     */
    public function addTramites($params, $puntoAtencionId, $id, $success, $error)
    {
        $grupoTramitesSync = new GrupoTramitesSync(
            $this->puntoAtencionRepository,
            $this->tramiteRepository,
            $this->grupoTramitesRepository,
            $this->grupoTramitesValidator,
            $this->disponibilidadRepository,
            $this->turnoRepository
        );
        // validacion
        $validateResult = $grupoTramitesSync->addTramites($params, $puntoAtencionId, $id);
        $this->redisServices->redisDelDispByPuntoDeAtencion($puntoAtencionId);
        return $this->processResult(
            $validateResult,
            function ($entity) use ($success) {
                return call_user_func($success, $this->tramiteRepository->flush());
            },
            $error
        );
    }
}

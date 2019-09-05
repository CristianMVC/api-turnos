<?php
namespace ApiV1Bundle\ApplicationServices;

use ApiV1Bundle\Entity\ValidateResultado;
use Symfony\Component\DependencyInjection\Container;
use ApiV1Bundle\Entity\Sync\AreaSync;
use ApiV1Bundle\Repository\OrganismoRepository;
use ApiV1Bundle\Repository\AreaRepository;
use ApiV1Bundle\Entity\Factory\AreaFactory;
use ApiV1Bundle\Entity\Validator\AreaValidator;

/**
 * Class AreaServices
 * @package ApiV1Bundle\ApplicationServices
 */

class AreaServices extends SNTServices
{
    /** @var OrganismoRepository  */
    private $organismoRepository;
    /** @var AreaRepository  */
    private $areaRepository;
    /** @var TramiteRepository  */
    private $tramiteRepository;
    /** @var AreaValidator  */
    private $areaValidator;
    /** @var RolesServices  */
    private $rolesServices;

    /**
     * AreaServices constructor.
     * @param Container $container
     * @param OrganismoRepository $organismoRepository
     * @param AreaRepository $areaRepository
     * @param AreaValidator $areaValidator
     * @param RolesServices $rolesServices
     */
    public function __construct(
        Container $container,
        OrganismoRepository $organismoRepository,
        AreaRepository $areaRepository,
        AreaValidator $areaValidator,
        RolesServices $rolesServices

    ) {
        parent::__construct($container);
        $this->organismoRepository = $organismoRepository;
        $this->areaRepository = $areaRepository;
        $this->areaValidator = $areaValidator;
        $this->rolesServices = $rolesServices;

    }

    /**
     * Listado de areas por organismo
     *
     * @param integer $organismoUrlId Identificador único de organismo
     * @param integer $limit Cantidad máxima de registros a retornar
     * @param integer $offset Cantidad de registros a saltar
     * @param string $authorization token del usuario logueado
     * @param callback $onError Callback para devolver respuesta fallida
     * @return mixed
     */
    public function findAllPaginate($organismoUrlId, $limit, $offset, $authorization, $onError)
    {
        $validateResultado = $this->rolesServices->getUsuario($authorization);
        $result = [];
        $resultset = [];

        $organismoId = (is_null($validateResultado->getEntity()->getOrganismoId()))?  $organismoUrlId : $validateResultado->getEntity()->getOrganismoId();

        if (! $validateResultado->hasError()) {

            $result = $this->areaRepository->findAllPaginate(
                $organismoId,
                $validateResultado->getEntity()->getAreaId(),
                $limit,
                $offset
            );

            $resultset = [
                'resultset' => [
                    'count' => $this->areaRepository->getTotal(
                        $organismoId,
                        $validateResultado->getEntity()->getAreaId()),
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
     * Puntos de atención por Area
     *
     * @param integer $organismoId Identificador único de organismo
     * @param integer $areaId Identificador único de área
     * @param integer $limit Cantidad máxima de registros a retornar
     * @param integer $offset Cantidad de registros a saltar
     * @param string $authorization token del usuario logueado
     * @param callback $onError Callback para devolver respuesta fallida
     * @return mixed
     */
    public function findPuntosAtencionPaginate($organismoId, $areaId, $limit, $offset, $authorization, $onError)
    {
        $validateResultado = $this->rolesServices->getUsuario($authorization);
        $result = [];
        $resultset = [];

        if (! $validateResultado->hasError()) {
            $puntoAtencionId = $validateResultado->getEntity()->getPuntoAtencionId();
            $result = $this->areaRepository->findPuntosAtencionPaginate($areaId, $limit, $offset, $puntoAtencionId);
            $resultset = [
                'resultset' => [
                    'count' => $this->areaRepository->getTotalPuntosAtencion($areaId,$puntoAtencionId),
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

        if($areaId == 'null') {

            $result = $this->areaRepository->findTramitesOrganismo($organismoId, $limit, $offset);

            $resultset = [
                'resultset' => [
                    'count' => $this->areaRepository->getTotalTramitesOrg($organismoId),
                    'offset' => $offset,
                    'limit' => $limit
                ]
            ];

        } else {

            $result = $this->areaRepository->findTramitesPaginate($areaId, $limit, $offset);
            $resultset = [
                'resultset' => [
                    'count' => $this->areaRepository->getTotalTramites($areaId),
                    'offset' => $offset,
                    'limit' => $limit
                ]
            ];

        }


        return $this->respuestaData($resultset, $result);
    }

    /**
     * Obtener un area del organismo
     *
     * @param integer $organismoId Identificador único del Organismo
     * @param integer $id Identificador único del área
     * @param callback $success Callback para devolver respuesta exitosa
     * @param callback $error Callback para devolver respuesta fallida
     * @return mixed
     */
    public function get($organismoId, $id, $success, $error)
    {
        $result = [];
        $area = $this->areaRepository->find($id);
        $validateResultado = $this->areaValidator->validarEntidad($area, "Área no encontrada");
        if (!$validateResultado->hasError()) {
            if ($area && $area->getOrganismo()->getId() == $organismoId) {
                $result = [
                    'id' => $area->getId(),
                    'abreviatura' => $area->getAbreviatura(),
                    'nombre' => $area->getNombre()
                ];
            } else {
                $validateResultado = new ValidateResultado(null, ["El área no pertenece a ese organismo"]);
            }
        }

        return $this->processResult(
            $validateResultado,
            function () use ($success, $result) {
                return call_user_func($success, $result);
            },
            $error
        );
    }

    /**
     * Crear un área para un organismo dado
     *
     * @param array $params array con los datos del área a crear
     * @param integer $organismoId Identificador único del organismo
     * @param callback $success Callback para devolver respuesta exitosa
     * @param callback $error Callback para devolver respuesta fallida
     * @return mixed
     */
    public function create($params, $organismoId, $success, $error)
    {
        $areaFactory = new AreaFactory($this->organismoRepository, $this->areaValidator);
        $validateResult = $areaFactory->create($params, $organismoId);
        return $this->processResult(
            $validateResult,
            function ($entity) use ($success) {
                return call_user_func($success, $this->organismoRepository->save($entity));
            },
            $error
        );
    }

    /**
     * Editar un área
     *
     * @param array $params array con los datos del área a modificar
     * @param integer $organismoId Identificador único del organismo
     * @param integer $id Identificador único del área
     * @param callback $success Callback para devolver respuesta exitosa
     * @param callback $error Callback para devolver respuesta fallida
     * @return mixed
     */
    public function edit($params, $organismoId, $id, $success, $error)
    {
        $areaSync = new AreaSync($this->organismoRepository, $this->areaRepository, $this->areaValidator);

        $validateResult = $areaSync->edit($organismoId, $id, $params);
        return $this->processResult(
            $validateResult,
            function ($entity) use ($success) {
                return call_user_func($success, $this->areaRepository->flush());
            },
            $error
        );
    }

    /**
     * Elimina un área
     *
     * @param integer $organismoId Identificador único del organismo
     * @param integer $id Identificador único del área
     * @param callback $success Callback para devolver respuesta exitosa
     * @param callback $error Callback para devolver respuesta fallida
     * @return mixed
     */
    public function delete($organismoId, $id, $success, $error)
    {

        $areaSync = new AreaSync($this->organismoRepository, $this->areaRepository, $this->areaValidator);
        $validateResult = $areaSync->delete($organismoId, $id);

        return $this->processResult(
            $validateResult,
            function ($entity) use ($success) {
                return call_user_func($success, $this->areaRepository->remove($entity));
            },
            $error
        );
    }

    /**

     * Buscar un área por abreviatura y nombre
     *
     * @param array $params Array con los parámetros necesarios para realizar la búsqueda
     * @param integer $limit Cantidad máxima de registros a retornar
     * @param integer $offset Cantidad de registros a saltar
     * @param callback $onError Callback para devolver respuesta fallida
     * @return mixed
     */
    public function search($params, $offset, $limit, $onError)
    {
        $response = [];
        $validateResultado = $this->areaValidator->validarBusquedaParams($params);

        if (! $validateResultado->hasError()) {
            $result = $this->areaRepository->searchAreaByNombreAbreviatura($params['q'], $offset, $limit);
            $validateResultado = $this->areaValidator->validarSearch($result);
            if (! $validateResultado->hasError()) {
                $resultset = [
                    'resultset' => [
                        'count' => $this->areaRepository->getTotalSearch($params['q']),
                        'offset' => $offset,
                        'limit' => $limit
                    ]
                ];
                $response = $this->respuestaData($resultset, $result);
            }
        }

        return $this->processError(
            $validateResultado,
            function () use ($response) {
                return $response;
            },
            $onError
        );
    }

     /** Buscar un trámite del área por nombre
     *
     * @param integer $organismoId Identificador único del organismo
     * @param integer $id Identificador único del área
     * @param array $params Array con datos para la búsqueda
     * @param callback $onError Callback para devolver respuesta fallida
     * @return mixed
     */
    public function buscarTramites($organismoId, $id, $params, $onError)
    {
        $response = [];
        $validateResultado = $this->areaValidator->validarBusquedaParams($params);

        if (! $validateResultado->hasError()) {
            $response = $this->areaRepository->search($params['q'], $id);
        }
        return $this->processError(
            $validateResultado,
            function () use ($response) {
                return $this->respuestaData([], $response);
            },
            $onError
        );
    }
}

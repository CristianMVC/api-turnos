<?php
namespace ApiV1Bundle\ApplicationServices;

use ApiV1Bundle\Entity\Factory\OrganismoFactory;
use ApiV1Bundle\Entity\Sync\OrganismoSync;
use ApiV1Bundle\Entity\Validator\OrganismoValidator;
use ApiV1Bundle\Repository\OrganismoRepository;
use Symfony\Component\DependencyInjection\Container;

/**
 * Class OrganismoServices
 * @package ApiV1Bundle\ApplicationServices
 */

class OrganismoServices extends SNTServices
{
    /** @var OrganismoRepository  */
    private $organismoRepository;
    /** @var OrganismoValidator  */
    private $organismoValidator;
    /** @var RolesServices  */
    private $rolesServices;

    /**
     * OrganismoServices constructor.
     * @param Container $container
     * @param OrganismoRepository $organismoRepository
     * @param OrganismoValidator $organismoValidator
     * @param RolesServices $rolesServices
     */
    public function __construct(
        Container $container,
        OrganismoRepository $organismoRepository,
        OrganismoValidator $organismoValidator,
        RolesServices $rolesServices
    ) {
        parent::__construct($container);
        $this->organismoRepository = $organismoRepository;
        $this->organismoValidator = $organismoValidator;
        $this->rolesServices = $rolesServices;
    }

    /**
     * Listar organismos
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
            $result = $this->organismoRepository->findAllPaginate($offset, $limit, $validateResultado->getEntity());
            $resultset = [
                'resultset' => [
                    'count' => $this->organismoRepository->getTotal($validateResultado->getEntity()),
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
     * Obtener un organismo
     *
     * @param integer $id Identificador único del organismo
     * @param callback $success Callback para devolver respuesta exitosa
     * @param callback $error Callback para devolver respuesta fallida
     * @return mixed
     */
    public function get($id, $success, $error)
    {
        $result = [];
        $organismo = $this->organismoRepository->find($id);
        $validateResultado = $this->organismoValidator->validarEntidad($organismo, "El organismo no existe");
        if (!$validateResultado->hasError()) {
            $result = [
                'id' => $organismo->getId(),
                'abreviatura' => $organismo->getAbreviatura(),
                'nombre' => $organismo->getNombre()
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
     * Crear un organismo
     *
     * @param array $params Array con los parámetros del organismo a crear
     * @param callback $success Callback para devolver respuesta exitosa
     * @param callback $error Callback para devolver respuesta fallida
     * @return mixed
     */
    public function create($params, $success, $error)
    {
        $organismoFactory = new OrganismoFactory($this->organismoValidator);
        $validateResult = $organismoFactory->create($params);
        return $this->processResult(
            $validateResult,
            function ($entity) use ($success) {
                return call_user_func($success, $this->organismoRepository->save($entity));
            },
            $error
        );
    }

    /**
     * Editar organismo
     *
     * @param array $params Array con los parámetros del organismo a editar
     * @param integer $id Identificador único del organismo
     * @param callback $success Callback para devolver respuesta exitosa
     * @param callback $error Callback para devolver respuesta fallida
     * @return mixed
     */
    public function edit($params, $id, $success, $error)
    {
        $organismoSync = new OrganismoSync($this->organismoRepository, $this->organismoValidator);
        $validateResult = $organismoSync->edit($id, $params);
        return $this->processResult(
            $validateResult,
            function ($entity) use ($success) {
                return call_user_func($success, $this->organismoRepository->flush());
            },
            $error
        );
    }

    /**
     * Eliminar organismo
     *
     * @param integer $id Identificador único del organismo
     * @param callback $success Callback para devolver respuesta exitosa
     * @param callback $error Callback para devolver respuesta fallida
     * @return mixed
     */
    public function delete($id, $success, $error)
    {
        $organismoSync = new OrganismoSync($this->organismoRepository, $this->organismoValidator);
        $validateResult = $organismoSync->delete($id);
        return $this->processResult(
            $validateResult,
            function ($entity) use ($success) {
                return call_user_func($success, $this->organismoRepository->remove($entity));
            },
            $error
        );
    }

    /**
     * Buscar un Organismo por nombre y abreviatura
     *
     * @param array $params arreglo con el Nombre del Organismo y la Abreviatura del Organismo
     * @param integer $limit Cantidad máxima de registros a retornar
     * @param integer $offset Cantidad de registros a saltar
     * @param callback $onError Callback para devolver respuesta fallida
     * @return mixed
     */
    public function search($params, $offset, $limit, $onError)
    {
        $response = [];

        $validateResultado = $this->organismoValidator->validarBusquedaParams($params);
        if(! $validateResultado->hasError()) {
            $result = $this->organismoRepository->search($params['q'], $offset, $limit);
            $resultset = [
                'resultset' => [
                    'count' => $this->organismoRepository->getTotalSearch($params['q']),
                    'offset' => $offset,
                    'limit' => $limit
                ]
            ];
            $response = $this->respuestaData($resultset, $result);
        }
        return $this->processError(
            $validateResultado,
            function () use ($response) {
                return $response;
            },
            $onError
        );
    }

}

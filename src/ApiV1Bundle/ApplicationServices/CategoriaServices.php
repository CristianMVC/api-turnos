<?php

namespace ApiV1Bundle\ApplicationServices;

use ApiV1Bundle\Entity\Factory\CategoriasFactory;
use ApiV1Bundle\Entity\Sync\CategoriasSync;
use ApiV1Bundle\Entity\ValidateResultado;
use ApiV1Bundle\Entity\Validator\CategoriaValidator;
use ApiV1Bundle\Entity\Validator\PuntoAtencionValidator;
use ApiV1Bundle\Repository\CategoriaRepository;
use ApiV1Bundle\Repository\PuntoAtencionRepository;
use ApiV1Bundle\Repository\TramiteRepository;
use Symfony\Component\DependencyInjection\Container;

/**
 * Class CategoriaServices
 * @package ApiV1Bundle\ApplicationServices
 */
class CategoriaServices extends SNTServices
{
    /** @var PuntoAtencionRepository  */
    private $puntoAtencionRepository;
    /** @var TramiteRepository  */
    private $tramiteRepository;
    /** @var CategoriaValidator  */
    private $categoriaValidator;
    /** @var CategoriaRepository  */
    private $categoriaRepository;
    /** @var PuntoAtencionValidator  */
    private $puntoAtencionValidator;

    /**
     * CategoriaServices constructor.
     * @param Container $container
     * @param CategoriaRepository $categoriaRepository
     * @param CategoriaValidator $categoriaValidator
     * @param PuntoAtencionRepository $puntoAtencionRepository
     * @param TramiteRepository $tramiteRepository
     * @param PuntoAtencionValidator $puntoAtencionValidator
     */
    public function __construct(
        Container $container,
        CategoriaRepository $categoriaRepository,
        CategoriaValidator $categoriaValidator,
        PuntoAtencionRepository $puntoAtencionRepository,
        TramiteRepository $tramiteRepository,
        PuntoAtencionValidator $puntoAtencionValidator
    )
    {
        parent::__construct($container);
        $this->categoriaRepository = $categoriaRepository;
        $this->categoriaValidator = $categoriaValidator;
        $this->puntoAtencionRepository = $puntoAtencionRepository;
        $this->tramiteRepository = $tramiteRepository;
        $this->puntoAtencionValidator = $puntoAtencionValidator;
    }

    /**
     * crear categoría
     *
     * @param array $params Array con datos para la búsqueda
     * @param integer $puntoAtencionId Identificador único del punto de atención
     * @param callback $sucess Callback para devolver respuesta exitosa
     * @param callback $error Callback para devolver respuesta fallida
     * @return mixed
     * @throws \Doctrine\ORM\NoResultException
     * @throws \Doctrine\ORM\NonUniqueResultException
     */
    public function create($params, $puntoAtencionId, $sucess, $error)
    {
        $factory = new CategoriasFactory(
            $this->puntoAtencionRepository,
            $this->tramiteRepository,
            $this->categoriaValidator
        );
        $this->categoriaRepository->beginTransaction();

        $validateResultado = $factory->create($params, $puntoAtencionId);
        if (!$validateResultado->hasError()) {
            $categoria = $validateResultado->getEntity();
            $this->categoriaRepository->save($categoria);
            $this->categoriaRepository->commit();
        } else {
            $this->categoriaRepository->rollback();
        }

        return $this->processResult(
            $validateResultado,
            function ($entity) use ($sucess) {
                return call_user_func($sucess, $entity);
            },
            $error
        );
    }

    /**
     * Editar categoría
     *
     * @param array $params Array con datos para la búsqueda
     * @param integer $puntoAtencionId Identificador único del punto de atención
     * @param integer $categoriaId Identificador único de categoría
     * @param callback $sucess Callback para devolver respuesta exitosa
     * @param callback $error Callback para devolver respuesta fallida
     * @return mixed
     */
    public function edit($params, $puntoAtencionId, $categoriaId, $sucess, $error)
    {
        $sync = new CategoriasSync(
            $this->puntoAtencionRepository,
            $this->tramiteRepository,
            $this->categoriaValidator,
            $this->categoriaRepository
        );

        $validateResultado = $sync->edit($params, $puntoAtencionId, $categoriaId);

        return $this->processResult(
            $validateResultado,
            function ($entity) use ($sucess) {
                $this->categoriaRepository->flush();
                return call_user_func($sucess, $entity);
            },
            $error
        );
    }

    /**
     * Eliminar categoría
     *
     * @param integer $puntoAtencionId Identificador único del punto de atención
     * @param integer $categoriaId Identificador único de categoría
     * @param callback $success Callback para devolver respuesta exitosa
     * @param callback $error Callback para devolver respuesta fallida
     * @return mixed
     */
    public function delete($puntoAtencionId, $categoriaId, $success, $error)
    {
        $sync = new CategoriasSync(
            $this->puntoAtencionRepository,
            $this->tramiteRepository,
            $this->categoriaValidator,
            $this->categoriaRepository
        );

        $validateResultado = $sync->delete($puntoAtencionId, $categoriaId);

        return $this->processResult(
            $validateResultado,
            function ($entity) use ($success) {
                $this->categoriaRepository->remove($entity);
                $this->categoriaRepository->flush();
                return call_user_func($success);
            },
            $error
        );
    }

    /**
     * Listar Categorías por punto de atención
     *
     * @param integer $limit Cantidad máxima de registros a retornar
     * @param integer $offset Cantidad de registros a saltar
     * @param integer $puntoAtencionId Identificador único del punto de atención
     * @param callback $error Callback para devolver respuesta fallida
     * @return mixed
     * @throws \Doctrine\ORM\NoResultException
     * @throws \Doctrine\ORM\NonUniqueResultException
     */
    public function getAllPaginated($offset = 0, $limit = 10, $puntoAtencionId, $error)
    {
        $result = $this->categoriaRepository->findAllPaginated($offset, $limit, $puntoAtencionId);
        $count = $this->categoriaRepository->getTotal($puntoAtencionId);

        $resultset = [
            'resultset' => [
                'count' => $count,
                'offset' => $offset,
                'limit' => $limit
            ]
        ];

        return $this->respuestaData($resultset, ($count) ? $result : []);
    }

    /**
     * obtener una categoría
     *
     * @param integer $puntoAtencionId Identificador único del punto de atención
     * @param integer $categoriaId Identificador único de categoría
     * @param callback $success Callback para devolver respuesta exitosa
     * @param callback $error Callback para devolver respuesta fallida
     * @return mixed
     */
    public function get($puntoAtencionId, $categoriaId, $success, $error)
    {
        $result = [];
        $categoria = $this->categoriaRepository->find($categoriaId);
        $validateResultado = $this->categoriaValidator->validarEntidad($categoria, "Categoría no encontrada");

        if (!$validateResultado->hasError()) {
            if ($categoria && $categoria->getPuntoAtencion()->getId() == $puntoAtencionId) {
                $tramites = [];
                foreach ($categoria->getTramites() as $tramite) {
                    $tramites[] = [
                        'id' => $tramite->getId(),
                        'nombre' => $tramite->getNombre()
                    ];
                }

                $result = [
                    'id' => $categoria->getId(),
                    'nombre' => $categoria->getNombre(),
                    'tramites' => $tramites
                ];
            } else {
                $validateResultado = new ValidateResultado(null, ['La categoría no pertenece a este punto de atención']);
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
     * Listar trámites sin categoría por punto de atención
     *
     * @param integer $puntoAtencionId Identificador único del punto de atención
     * @param callback $error Callback para devolver respuesta fallida
     * @return mixed
     * @throws \Doctrine\ORM\NoResultException
     * @throws \Doctrine\ORM\NonUniqueResultException
     */
    public function getTramitesSinCategoriaPaginated($puntoAtencionId, $error)
    {
        $puntoAtencion = $this->puntoAtencionRepository->find($puntoAtencionId);
        $validateResultado = $this->puntoAtencionValidator->verificaPuntoAtencion($puntoAtencion);

        $response = [];
        if (!$validateResultado->hasError()) {
            $sync = new CategoriasSync(
                $this->puntoAtencionRepository,
                $this->tramiteRepository,
                $this->categoriaValidator,
                $this->categoriaRepository
            );

            $listaTramites = $sync->getTramitesDisponibles($puntoAtencion);
            $response = $this->respuestaData([], $listaTramites);
        }

        return $this->processError(
            $validateResultado,
            function () use ($response) {
                return $response;
            },
            $error
        );
    }
}

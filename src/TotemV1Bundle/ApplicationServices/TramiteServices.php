<?php
namespace TotemV1Bundle\ApplicationServices;

use SNT\Domain\Services\Parser;
use ApiV1Bundle\Entity\Tramite;
use ApiV1Bundle\Entity\ValidateResultado;
use ApiV1Bundle\Repository\TramiteRepository;
use Symfony\Component\DependencyInjection\Container;
use TotemV1Bundle\Entity\Validator\TramiteTotemValidator;
use TotemV1Bundle\ExternalServices\TurnoTotemIntegration;
use TotemV1Bundle\Helper\ServicesHelper;

/**
 * Class TramiteServices
 * @package TotemV1Bundle\ApplicationServices
 */
class TramiteServices extends TotemServices
{
    /** @var \TotemV1Bundle\Repository\TramiteRepository  */
    private $tramiteRepository;

    /** @var TramiteTotemValidator */
    private $tramiteTotemValidator;

    /** @var TurnoTotemIntegration */
    private $turnoTotemIntegration;

    /**
     * @var Parser
     */
    private $parserService;

    /**
     * TramiteServices constructor.
     *
     * @param Container $container
     * @param TramiteRepository $tramiteRepository
     * @param TramiteTotemValidator $tramiteTotemValidator
     * @param TurnoTotemIntegration $turnoTotemIntegration
     */
    public function __construct(
        Container $container,
        TramiteRepository $tramiteRepository,
        TramiteTotemValidator $tramiteTotemValidator,
        TurnoTotemIntegration $turnoTotemIntegration,
        Parser $parserService
    ) {
        parent::__construct($container);
        $this->tramiteRepository = $tramiteRepository;
        $this->tramiteTotemValidator = $tramiteTotemValidator;
        $this->turnoTotemIntegration = $turnoTotemIntegration;
        $this->parserService = $parserService;
    }

    /**
     * Encontrar trámites por nombre y punto de atención y paginar
     *
     * @param integer $puntoAtencionId ID del punto de atención
     * @param string $nombre Filtro de nombre de trámites
     * @param integer $limit Límite de registros a mostrar
     * @param integer $offset Valor por defecto desde que dato comienza a retornar
     * @return mixed
     */
    public function findTramitesByPuntoNombrePaginate($puntoAtencionId, $nombre, $limit, $offset)
    {
        $result = $this->tramiteRepository->findTramitesTotemByPuntoNombrePaginate(
            $puntoAtencionId,
            $nombre,
            $limit,
            $offset
        );
        $resultset = [
            'resultset' => [
                'count' => $this->tramiteRepository->getTotalTotemPuntoNombre($puntoAtencionId, $nombre),
                'offset' => $offset,
                'limit' => $limit
            ]
        ];
        return $this->respuestaData($resultset, $result);
    }

    /**
     * Listado de trámites por categoría
     *
     * @param integer $categoriaId ID de la categoría
     * @param string $query Filtro de nombre de trámites
     * @param integer $limit Límite de registros a mostrar
     * @param integer $offset Valor por defecto desde que dato comienza a retornar
     * @return mixed
     */
    public function findTramitesByCategoriaPaginate($puntoAtencionId, $categoriaId, $limit, $offset)
    {
        $result = $this->tramiteRepository->findTramitesTotemByCategoriaPaginate(
            $puntoAtencionId,
            $categoriaId,
            $limit,
            $offset
        );
        $resultset = [
            'resultset' => [
                'count' => $this->tramiteRepository->getTotalTotemCategoria($puntoAtencionId, $categoriaId),
                'offset' => $offset,
                'limit' => $limit
            ]
        ];
        return $this->respuestaData($resultset, $result);
    }

    /**
     * Obtener un trámite dado su ID y el punto de atención
     *
     * @param integer $tramiteId Identificador único del trámite del que se quiere obtener información
     * @param integer $puntoAtencionId Identificador único del punto de atención
     * @return mixed
     */
    public function get($puntoAtencionId, $tramiteId)
    {
        $tramite = $this->tramiteRepository->find($tramiteId);
        if ($tramite) {
            $grupoTramite = $this->tramiteRepository->getGrupotramiteIdByPunto($puntoAtencionId, $tramiteId);
            return $this->respuestaData([], [
                'id' => $tramite->getId(),
                'argentinaGobArId' => $tramite->getIdArgentinaGobAr(),
                'nombre' => $tramite->getNombre(),
                'duracion' => $tramite->getDuracion(),
                'requisitos' => $this->parserService->render($tramite->getRequisitos()),
                'visibilidad' => $tramite->getVisibilidad(),
                'grupoTramiteId' => $grupoTramite['grupoTramiteId']
            ]);
        }
        return $this->respuestaData([], null);
    }

    /**
     * Get cantidad de turnos delante
     *
     * @param array $params arreglo con los datos (punto de atención y grupo trámite)
     * @param callback $onError Callback para devolver respuesta fallida
     * @return mixed
     */
    public function getCantidadDelante($params, $onError)
    {
        $validateResult = $this->tramiteTotemValidator->validarTramite($params);
        $result = null;

        if (! $validateResult->hasError()) {
            try {
                // send the data to SNC
                $validateResult = $this->turnoTotemIntegration->getCantidadDelante($params);

                if (!$validateResult->hasError()) {
                    $response = $validateResult->getEntity();
                    $result = [
                        'ciudadanos_delante' => $response['additional']['porDelante']
                    ];
                }
            } catch (Exception $exception) {
                $validateResult = new ValidateResultado(null, [$exception->getMessage()]);
            }
        }

        return $this->processResult(
            $validateResult,
            function () use ($result) {
                return $this->respuestaData([], $result);
            },
            $onError
        );
    }

    /**
     * Obtener campos del trámite
     *
     * @param integer $tramiteId identificador único de trámite
     * @param callback $sucess Callback para devolver respuesta exitosa
     * @param callback $error Callback para devolver respuesta fallida
     * @return mixed
     */
    public function getCampos($tramiteId, $success, $error)
    {
        $result = [];
        /** @var Tramite $tramite */
        $tramite = $this->tramiteRepository->find($tramiteId);
        $validateResultado = $this->tramiteTotemValidator->validarEntidad($tramite, "El trámite no existe");

        if (!$validateResultado->hasError()) {

            $documento = 'CUIL';
            if ($tramite->getExcepcional()) {
                $documento = 'Documento extranjero';
            }

            $result['nombre'] = [
                'key' => 'nombre',
                'label' => 'Nombre',
                'required' => true,
                'type' => 'textbox',
                'order' => 1,
                'description' => '',
                'formComponent' => [
                    'typeValue' => 'text'
                ]
            ];

            $result['apellido'] = [
                'key' => 'apellido',
                'label' => 'Apellido',
                'required' => true,
                'type' => 'textbox',
                'order' => 2,
                'description' => '',
                'formComponent' => [
                    'typeValue' => 'text'
                ]
            ];

            $result['cuil'] = [
                'key' => 'cuil',
                'label' => $documento,
                'required' => true,
                'type' => 'textbox',
                'order' => 3,
                'description' => '',
                'formComponent' => [
                    'typeValue' => 'text'
                ]
            ];
        }

        return $this->processResult(
            $validateResultado,
            function () use ($success, $result) {
                return $success($result);
            },
            $error
        );
    }
}

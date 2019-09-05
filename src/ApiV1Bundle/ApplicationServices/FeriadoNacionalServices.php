<?php

namespace ApiV1Bundle\ApplicationServices;

use ApiV1Bundle\Entity\Factory\FeriadoNacionalFactory;
use ApiV1Bundle\Entity\Sync\FeriadoNacionalSync;
use ApiV1Bundle\Entity\Validator\FeriadoNacionalValidator;
use ApiV1Bundle\Repository\DiaNoLaborableRepository;
use ApiV1Bundle\Repository\FeriadoNacionalRepository;
use ApiV1Bundle\Repository\PuntoAtencionRepository;
use Symfony\Component\DependencyInjection\Container;
use ApiV1Bundle\Helper\ServicesHelper;

/**
 * Class FeriadoNacionalServices
 * @package ApiV1Bundle\ApplicationServices
 */
class FeriadoNacionalServices extends SNTServices
{
    /** @var FeriadoNacionalRepository  */
    private $feriadoNacionalRepository;
    /** @var FeriadoNacionalValidator  */
    private $feriadoNacionalValidator;
    /** @var PuntoAtencionRepository  */
    private $puntoAtencionRepository;
    /** @var DiaNoLaborableRepository  */
    private $diasNoLaborablesRepository;

    /**
     * FeriadoNacionalServices constructor.
     * @param Container $container
     * @param FeriadoNacionalRepository $feriadoNacionalRepository
     * @param FeriadoNacionalValidator $feriadoNacionalValidator
     * @param PuntoAtencionRepository $puntoAtencionRepository
     * @param DiaNoLaborableRepository $diasNoLaborablesRepository
     */
    public function __construct(
        Container $container,
        FeriadoNacionalRepository $feriadoNacionalRepository,
        FeriadoNacionalValidator $feriadoNacionalValidator,
        PuntoAtencionRepository $puntoAtencionRepository,
        DiaNoLaborableRepository $diasNoLaborablesRepository
    ) {
        parent::__construct($container);
        $this->feriadoNacionalRepository = $feriadoNacionalRepository;
        $this->feriadoNacionalValidator = $feriadoNacionalValidator;
        $this->puntoAtencionRepository = $puntoAtencionRepository;
        $this->diasNoLaborablesRepository = $diasNoLaborablesRepository;
    }

    /**
     * Crear un Feriado Nacional
     *
     * @param array $params Array con la fecha del Feriado Nacional
     * @param callback $sucess Callback para devolver respuesta exitosa
     * @param callback $error Callback para devolver respuesta fallida
     * @return mixed
     */
    public function create($params, $sucess, $error)
    {
        $feriadoFactory = new FeriadoNacionalFactory(
            $this->feriadoNacionalValidator,
            $this->feriadoNacionalRepository,
            $this->puntoAtencionRepository,
            $this->diasNoLaborablesRepository
        );

        $validateResult = $feriadoFactory->create($params);

        return $this->processResult(
            $validateResult,
            function ($entity) use ($sucess) {
                return call_user_func($sucess, $this->feriadoNacionalRepository->save($entity));
            },
            $error
        );
    }

    /**
     * Eliminar un Feriado Nacional
     *
     * @param date $fecha fecha a eliminar
     * @param callback $sucess Callback para devolver respuesta exitosa
     * @param callback $error Callback para devolver respuesta fallida
     * @return mixed
     */
    public function delete($fecha, $sucess, $error)
    {
        $feriadoSync = new FeriadoNacionalSync(
            $this->feriadoNacionalValidator,
            $this->feriadoNacionalRepository,
            $this->puntoAtencionRepository,
            $this->diasNoLaborablesRepository
        );

        $validateResult = $feriadoSync->delete($fecha);

        return $this->processResult(
            $validateResult,
            function ($entity) use ($sucess) {
                return call_user_func($sucess, $this->feriadoNacionalRepository->remove($entity));
            },
            $error
        );
    }

    /**
     * Obtener el listado de feriados nacionales
     *
     * @return mixed
     */
    public function getAllFeriadoNacional()
    {
        $result = [];
        $feriados = $this->feriadoNacionalRepository->findAll();

        foreach ($feriados as $feriado) {
            $result[] = $feriado->getFecha()->format('Y-m-d');
        }

        $resultset = [
            'resultset' => [
                'count' => count($feriados),
                'offset' => 0,
                'limit' => count($feriados)
            ]
        ];

        return $this->respuestaData($resultset, $result);
    }
}

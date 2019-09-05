<?php
/**
 * Created by PhpStorm.
 * User: Javier
 * Date: 26/12/2017
 * Time: 3:20 PM
 */
namespace ApiV1Bundle\Entity\Sync;

use ApiV1Bundle\Entity\DiaNoLaborable;
use ApiV1Bundle\Repository\DiaNoLaborableRepository;
use ApiV1Bundle\Repository\FeriadoNacionalRepository;

/**
 * Class DiaNoLaborableSync
 * @package ApiV1Bundle\Entity\Sync
 */
class DiaNoLaborableSync
{
    /** @var FeriadoNacionalRepository  */
    private $feriadoNacionalRepository;
    /** @var DiaNoLaborableRepository  */
    private $diaNoLaborableRepository;

    /**
     * DiaNoLaborableSync constructor.
     * @param FeriadoNacionalRepository $feriadoNacionalRepository
     * @param DiaNoLaborableRepository $diasNoLaborablesRepository
     */
    public function __construct(
        FeriadoNacionalRepository $feriadoNacionalRepository,
        DiaNoLaborableRepository $diasNoLaborablesRepository
    ) {
        $this->feriadoNacionalRepository = $feriadoNacionalRepository;
        $this->diaNoLaborableRepository = $diasNoLaborablesRepository;
    }


    /**
     * Agrega todos los feriados como Día No Laborable a un Punto de Atencion
     * @param object $puntoAtencion objeto punto de atención
     */
    public function add($puntoAtencion)
    {
        $feriados = $this->feriadoNacionalRepository->findAll();
        foreach ($feriados as $feriado) {
            $diaNoLaborable = new DiaNoLaborable($feriado->getFecha(), $puntoAtencion);
            $this->diaNoLaborableRepository->save($diaNoLaborable);
        }
    }
}
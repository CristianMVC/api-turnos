<?php
namespace ApiV1Bundle\Entity\Sync;

use ApiV1Bundle\Entity\ValidateResultado;
use ApiV1Bundle\Entity\Validator\FeriadoNacionalValidator;
use ApiV1Bundle\Repository\DiaNoLaborableRepository;
use ApiV1Bundle\Repository\FeriadoNacionalRepository;
use ApiV1Bundle\Repository\PuntoAtencionRepository;

/**
 * Class FeriadoNacionalSync
 * @package ApiV1Bundle\Entity\Sync
 */
class FeriadoNacionalSync
{
    /** @var FeriadoNacionalValidator  */
    private $feriadoNacionalValidator;
    /** @var FeriadoNacionalRepository  */
    private $feriadoNacionalRepository;
    /** @var PuntoAtencionRepository  */
    private $puntoAtencionRepository;
    /** @var DiaNoLaborableRepository  */
    private $diaNoLaborableRepository;

    /**
     * FeriadoNacionalSync constructor.
     * @param FeriadoNacionalValidator $feriadoNacionalValidator
     * @param FeriadoNacionalRepository $feriadoNacionalRepository
     * @param PuntoAtencionRepository $puntoAtencionRepository
     * @param DiaNoLaborableRepository $diasNoLaborablesRepository
     */
    public function __construct(
        FeriadoNacionalValidator $feriadoNacionalValidator,
        FeriadoNacionalRepository $feriadoNacionalRepository,
        PuntoAtencionRepository $puntoAtencionRepository,
        DiaNoLaborableRepository $diasNoLaborablesRepository
    ) {
        $this->feriadoNacionalValidator = $feriadoNacionalValidator;
        $this->feriadoNacionalRepository = $feriadoNacionalRepository;
        $this->puntoAtencionRepository = $puntoAtencionRepository;
        $this->diaNoLaborableRepository = $diasNoLaborablesRepository;
    }

    /**
     * Eliminar un Feriado Nacional.
     *
     * @param \date $fecha fecha a eliminar
     * @return ValidateResultado
     */
    public function delete($fecha)
    {
        $validateResultado = $this->feriadoNacionalValidator->validarFecha($fecha);
        if (! $validateResultado->hasError()) {
            // eliminamos todos los días no laborables de la fecha
            $fecha = new \DateTime($fecha);
            $this->diaNoLaborableRepository->deleteDiaNoLaborable($fecha);
            return new ValidateResultado($validateResultado->getEntity(), []);
        }

        return $validateResultado;
    }
}

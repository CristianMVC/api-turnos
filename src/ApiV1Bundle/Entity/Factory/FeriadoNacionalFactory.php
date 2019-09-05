<?php

namespace ApiV1Bundle\Entity\Factory;

use ApiV1Bundle\Entity\DiaNoLaborable;
use ApiV1Bundle\Entity\FeriadoNacional;
use ApiV1Bundle\Entity\ValidateResultado;
use ApiV1Bundle\Entity\Validator\FeriadoNacionalValidator;
use ApiV1Bundle\Repository\DiaNoLaborableRepository;
use ApiV1Bundle\Repository\FeriadoNacionalRepository;
use ApiV1Bundle\Repository\PuntoAtencionRepository;
use Symfony\Component\Validator\Constraints\DateTime;

/**
 * Class FeriadoNacionalFactory
 * @package ApiV1Bundle\Entity\Factory
 */
class FeriadoNacionalFactory
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
     * FeriadoNacionalFactory constructor.
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
        $this->diaNoLaborableRepository  = $diasNoLaborablesRepository;
    }

    /**
     * Crea un Feriado Nacional
     *
     * @param array $params array con los datos del feriado a crear
     * @return mixed
     */
    public function create($params)
    {
        $validateResultado = $this->feriadoNacionalValidator->validarCreate($params);

        if (! $validateResultado->hasError()) {
            $fecha = new \DateTime($params['fecha']);

            $puntosAtencion = $this->puntoAtencionRepository->findAll();

            foreach ($puntosAtencion as $puntoAtencion) {
                //create dia no laborable
                $diaNoLaborable = new DiaNoLaborable($fecha, $puntoAtencion);
                //add dia no laborable al punto de atencion
                $puntoAtencion->addDiaNoLaborable($diaNoLaborable);
                $this->puntoAtencionRepository->persist($puntoAtencion);
            }

            $feriadoNacional = new FeriadoNacional($fecha);

            return new ValidateResultado($feriadoNacional, []);
        }

        $errors = $validateResultado->getErrors();
        return new ValidateResultado(null, $errors['errors']);
    }
}

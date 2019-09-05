<?php
namespace ApiV1Bundle\Entity\Factory;

use ApiV1Bundle\Entity\PuntoAtencion;
use ApiV1Bundle\Entity\ValidateResultado;
use ApiV1Bundle\Repository\GrupoTramiteRepository;
use ApiV1Bundle\Repository\TramiteRepository;
use ApiV1Bundle\Entity\GrupoTramite;
use ApiV1Bundle\Repository\PuntoAtencionRepository;
use ApiV1Bundle\Entity\Validator\GrupoTramitesValidator;
use ApiV1Bundle\Helper\ServicesHelper;
use ApiV1Bundle\Entity\Disponibilidad;
use ApiV1Bundle\Repository\DisponibilidadRepository;

/**
 * Class GrupoTramitesFactory
 * @package ApiV1Bundle\Entity\Factory
 */

class GrupoTramitesFactory
{
    /** @var PuntoAtencionRepository  */
    private $puntoAtencionRepository;
    /** @var GrupoTramiteRepository  */
    private $grupoTramitesRepository;
    /** @var TramiteRepository  */
    private $tramiteRepository;
    /** @var GrupoTramitesValidator  */
    private $grupoTramitesValidator;
    /** @var DisponibilidadRepository  */
    private $disponibilidadRepository;

    /**
     * GrupoTramitesFactory constructor.
     * @param PuntoAtencionRepository $puntoAtencionRepository
     * @param GrupoTramiteRepository $grupoTramitesRepository
     * @param TramiteRepository $tramiteRepository
     * @param GrupoTramitesValidator $grupoTramitesValidator
     * @param DisponibilidadRepository $disponibilidadRepository
     */
    public function __construct(
        PuntoAtencionRepository $puntoAtencionRepository,
        GrupoTramiteRepository $grupoTramitesRepository,
        TramiteRepository $tramiteRepository,
        GrupoTramitesValidator $grupoTramitesValidator,
        DisponibilidadRepository $disponibilidadRepository
    ) {
        $this->puntoAtencionRepository = $puntoAtencionRepository;
        $this->grupoTramitesRepository = $grupoTramitesRepository;
        $this->tramiteRepository = $tramiteRepository;
        $this->grupoTramitesValidator = $grupoTramitesValidator;
        $this->disponibilidadRepository = $disponibilidadRepository;
    }

    /**
     * Guardar disponibilidad
     *
     * @param object $puntoAtencion objeto puntoAtencion
     * @param object $grupoTramite objeto grupoTramite
     * @return mixed
     */
    private function saveDisponibilidad($puntoAtencion, $grupoTramite) {
        $horariosAtencion = $puntoAtencion->getHorariosAtencion();
        foreach ($horariosAtencion as $horarioAtencion) {
            $disponibilidad = new Disponibilidad($puntoAtencion, $grupoTramite, $horarioAtencion, 0);
            $this->disponibilidadRepository->persist($disponibilidad);
        }
    }

    /**
     * Crear un grupo de tramites
     * @param array $params array con los datos del grupo detrámites
     * @param integer $puntoAtencionId Identificador único del punto de atención
     * @return mixed
     */
    public function create($params, $puntoAtencionId)
    {
        // grupo de tramites
        $puntoAtencion = $this->puntoAtencionRepository->find($puntoAtencionId);
        $validateResultado = $this->grupoTramitesValidator->validarParams($params, $puntoAtencion);
        if(! $validateResultado->hasError()) {
            $intervalo = ServicesHelper::fraccionHoraria($params['intervalo']);
            $grupoTramites = new GrupoTramite($puntoAtencion, $params['nombre'], $params['horizonte'], (float) $intervalo);
            foreach ($params['tramites'] as $tramiteId) {
                $tramite = $this->tramiteRepository->find($tramiteId);
                $validateTramiteResultado = $this->grupoTramitesValidator->validarTramite(
                    $tramite,
                    $grupoTramites->getId(),
                    $puntoAtencionId
                );
                // valido los errores de los trámites
                if ($validateTramiteResultado->hasError()) {
                    $errors = $validateTramiteResultado->getErrors();
                    return new ValidateResultado(null, $errors['errors']);
                }
                // si no hubo errores, agrego el tramite al grupo
                $grupoTramites->addTramite($tramite);
            }

            $this->saveDisponibilidad($puntoAtencion, $grupoTramites);
            return new ValidateResultado($grupoTramites, []);
        }
        $errors = $validateResultado->getErrors();
        return new ValidateResultado(null, $errors['errors']);
    }
}

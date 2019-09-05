<?php
namespace ApiV1Bundle\Entity\Sync;

use ApiV1Bundle\Entity\ValidateResultado;
use ApiV1Bundle\Repository\DisponibilidadRepository;
use ApiV1Bundle\Repository\GrupoTramiteRepository;
use ApiV1Bundle\Repository\TramiteRepository;
use ApiV1Bundle\Repository\PuntoAtencionRepository;
use ApiV1Bundle\Entity\Validator\GrupoTramitesValidator;
use ApiV1Bundle\Helper\ServicesHelper;
use ApiV1Bundle\Repository\TurnoRepository;

/**
 * Class GrupoTramitesSync
 * @package ApiV1Bundle\Entity\Sync
 */

class GrupoTramitesSync
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
    /** @var TurnoRepository  */
    private $turnoRepository;

    /**
     * GrupoTramitesSync constructor.
     * @param PuntoAtencionRepository $puntoAtencionRepository
     * @param TramiteRepository $tramiteRepository
     * @param GrupoTramiteRepository $grupoTramitesRepository
     * @param GrupoTramitesValidator $grupoTramitesValidator
     * @param DisponibilidadRepository $disponibilidadRepository
     * @param TurnoRepository $turnoRepository
     */
    public function __construct(
        PuntoAtencionRepository $puntoAtencionRepository,
        TramiteRepository $tramiteRepository,
        GrupoTramiteRepository $grupoTramitesRepository,
        GrupoTramitesValidator $grupoTramitesValidator,
        DisponibilidadRepository $disponibilidadRepository,
        TurnoRepository $turnoRepository
    ) {
        $this->puntoAtencionRepository = $puntoAtencionRepository;
        $this->tramiteRepository = $tramiteRepository;
        $this->grupoTramitesRepository = $grupoTramitesRepository;
        $this->grupoTramitesValidator = $grupoTramitesValidator;
        $this->disponibilidadRepository = $disponibilidadRepository;
        $this->turnoRepository = $turnoRepository;
    }

    /**
     * Modificar grupo de tramites
     *
     * @param array $params array con datos del grupo de trámites
     * @param integer $puntoAtencionId Identificador único del punto de atención
     * @param integer $id Identificador único del grupo de trámites
     * @return mixed
     */
    public function edit($params, $puntoAtencionId, $id)
    {
        $validateResultado = $this->grupoTramitesValidator->validarEditar($params, $puntoAtencionId, $id);
        if (! $validateResultado->hasError()) {
            $grupoTramites = $this->grupoTramitesRepository->find($id);
            $grupoPuntoAtencionId = $grupoTramites->getPuntoAtencion()->getId();
            $validateResultado = $this->grupoTramitesValidator->validarGrupoPuntoAtencion($grupoPuntoAtencionId, $puntoAtencionId);
            // edito el grupo de tramites
            if (! $validateResultado->hasError()) {
                $grupoTramites->setNombre($params['nombre']);
                $grupoTramites->setHorizonte($params['horizonte']);
                $grupoTramites->setIntervaloTiempo((float) ServicesHelper::fraccionHoraria($params['intervalo']));
                // tramites
                $tramitesDelGrupo = [];
                // los puntos de atención a eliminar
                foreach ($grupoTramites->getTramites() as $tramite) {
                    // si no está en la lista que mandan, es porque lo quieren eliminar
                    if (! in_array($tramite->getId(), $params['tramites'])) {
                        if ($this->turnoRepository->findTotalTurnosByTramite($tramite->getId(), $grupoTramites->getId())) {
                            return new ValidateResultado(null, [
                                "No puede desasignar el trámite {$tramite->getNombre()} porque tiene turnos asignados"
                            ]);
                        };
                        $grupoTramites->removeTramite($tramite);
                    } else {
                        $tramitesDelGrupo[] = $tramite->getId();
                    }
                }
                // los tramites a agregar
                foreach ($params['tramites'] as $tramiteId) {
                    if (! in_array($tramiteId, $tramitesDelGrupo)) {
                        $tramite = $this->tramiteRepository->find($tramiteId);
                        $grupoTramites->addTramite($tramite);
                    }
                }
            }
            return new ValidateResultado($grupoTramites, []);
        }
        return $validateResultado;
    }

    /**
     * Eliminar grupo de tramites
     *
     * @param integer $puntoAtencionId Identificador único del punto de atención
     * @param integer $id Identificador único del grupo de trámites
     * @return mixed
     */
    public function delete($puntoAtencionId, $id)
    {
        $validateResultado = $this->grupoTramitesValidator->validarEliminar($id, $puntoAtencionId);
        if(! $validateResultado->hasError()) {
            $grupoTramites = $this->grupoTramitesRepository->find($id);
            $colDisponibilidad = $this->disponibilidadRepository->findBy(['puntoAtencion' => $puntoAtencionId, 'grupoTramite' => $id]);
            foreach ($colDisponibilidad as $disponibilidad) {
                $this->disponibilidadRepository->remove($disponibilidad);
            }
            $grupoTramites->clearTramites();
            return new ValidateResultado($grupoTramites, []);
        }
        return $validateResultado;
    }

    /**
     * Asignar tramites grupo de tramites
     *
     * @param array $params array con datos del grupo de trámites
     * @param integer $puntoAtencionId Identificador único del punto de atención
     * @param integer $id Identificador único del grupo de trámites
     * @return ValidateResultado
     */
    public function addTramites($params, $puntoAtencionId, $id)
    {
        $errors = [];
        // grupo de tramites
        $grupoTramites = $this->grupoTramitesRepository->find($id);
        if (! $grupoTramites || $grupoTramites->getPuntoAtencion()->getId() != $puntoAtencionId) {
            $errors[] = 'Grupo de tramites inexistente';
        }
        // edito el grupo de tramites
        if (! count($errors)) {
            // removemos todos los tramites del grupo
            $grupoTramites->clearTramites();
            // asociamos los tramites al grupo
            foreach ($params['tramites'] as $tramiteId) {
                $tramite = $this->tramiteRepository->find($tramiteId);
                if ($tramite) {
                    $tramiteError = $this->grupoTramitesValidator->validarTramite(
                        $tramite,
                        $grupoTramites->getId(),
                        $puntoAtencionId
                    );
                    if (! $tramiteError->hasError()) {
                        $grupoTramites->addTramite($tramite);
                    } else {
                        $errors[] = $tramiteError->getErrors()['errors'];
                    }
                } else {
                    $errors[] = 'El tramite con ID: ' . $tramiteId . ' no existe';
                }
            }
            // chequeo errores en los tramites
            if (! count($errors)) {
                return new ValidateResultado($grupoTramites, []);
            }
        }
        return new ValidateResultado(null, $errors);
    }
}

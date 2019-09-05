<?php
/**
 * Created by PhpStorm.
 * User: jtibi
 * Date: 10/4/2018
 * Time: 4:17 PM
 */
namespace ApiV1Bundle\Entity\Sync;

use ApiV1Bundle\Helper\ServicesHelper;
use ApiV1Bundle\Repository\GrupoTramiteRepository;

/**
 * Class GrupoTramiteIntervaloSync
 * @package ApiV1Bundle\Entity\Sync
 */
class GrupoTramiteIntervaloSync
{
    /** @var GrupoTramiteRepository */
    private $grupoTramitesRepository;

    /**
     * GrupoTramiteIntervaloSync constructor.
     * @param GrupoTramiteRepository $grupoTramiteRepository
     */
    public function __construct(
        GrupoTramiteRepository $grupoTramiteRepository
    )
    {
        $this->grupoTramitesRepository = $grupoTramiteRepository;
    }

    /**
     * Reinicar intervalos
     *
     * @param object $puntoAtencion objeto punto de atención
     * @param integer $intervalo cantidad de minutos
     */
    public function resetIntervalos($puntoAtencion, $intervalo)
    {
        foreach ($puntoAtencion->getGrupoTramites() as $grupoTramite) {
            if (ServicesHelper::transformaFracionHoraria($grupoTramite->getIntervaloTiempo()) > $intervalo) {
                $grupoTramite->setIntervaloTiempo((float) ServicesHelper::fraccionHoraria($intervalo));
            }
            $this->grupoTramitesRepository->save($grupoTramite);
        }
    }
}
<?php

namespace ApiV1Bundle\Entity;

use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Validator\Constraints as Assert;

/**
 * Class Disponibilidad
 * @package ApiV1Bundle\Entity
 *
 * @ORM\Table(name="disponibilidad")
 * @ORM\Entity(repositoryClass="ApiV1Bundle\Repository\DisponibilidadRepository")
 */

class Disponibilidad
{
    /**
     * Identificador único de disponibilidad, autoincremental
     *
     * @var int
     *
     * @ORM\Column(name="id", type="integer")
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="AUTO")
     */
    private $id;

    /**
     * Identificador para refereciar con la tabla de puntos de atención
     *
     * @var PuntoAtencion
     *
     * @Assert\NotNull(
     *     message="El punto de atención no puede ser vacío."
     * )
     * @Assert\Type(
     *     type="object",
     *     message="El punto de atención debe ser de un objeto."
     * )
     *
     * @ORM\ManyToOne(targetEntity="PuntoAtencion", inversedBy="turnosDisponibles")
     * @ORM\JoinColumn(name="punto_atencion_id", referencedColumnName="id")
     */
    private $puntoAtencion;

    /**
     * Identificador para referenciar un la disponibilidad con un grupo de trámites
     *
     * @var GrupoTramite
     *
     * @Assert\NotNull(
     *     message="El grupo tramite no puede ser vacío."
     * )
     * @Assert\Type(
     *     type="object",
     *     message="El grupo tramite debe ser de un objeto."
     * )
     *
     * @ORM\ManyToOne(targetEntity="GrupoTramite", inversedBy="turnosDisponibles")
     * @ORM\JoinColumn(name="grupo_tramite_id", referencedColumnName="id")
     */
    private $grupoTramite;

    /**
     * Referencia un horario de atención con la disponibilidad
     *
     * @var HorarioAtencion
     *
     * @Assert\NotNull(
     *     message="El horario de atención no puede ser vacío."
     * )
     * @Assert\Type(
     *     type="object",
     *     message="El horario de atención debe ser de un objeto."
     * )
     *
     * @ORM\ManyToOne(targetEntity="HorarioAtencion", inversedBy="turnosDisponibles")
     * @ORM\JoinColumn(name="horario_atencion_id", referencedColumnName="id")
     */
    private $horarioAtencion;

    /**
     * Cantidad de turnos dados para un horario de atención por día
     *
     * @var int
     *
     * @Assert\NotNull(
     *     message="La cantidad de turnos no puede ser vacío."
     * )
     * @Assert\Type(
     *     type="integer",
     *     message="La cantidad de turnos debe ser un valor númerico."
     * )
     *
     * @ORM\Column(name="cantidad_turnos", type="smallint")
     */
    private $cantidadTurnos;

    public function __construct($puntoAtencion, $grupoTramite, $horarioAtencion, $cantidadTurnos)
    {
        $this->puntoAtencion = $puntoAtencion;
        $this->grupoTramite = $grupoTramite;
        $this->horarioAtencion  = $horarioAtencion;
        $this->cantidadTurnos = $cantidadTurnos;
    }


    /**
     * Obtiene el Identificador único de disponibilidad
     *
     * Get id
     *
     * @return int
     */
    public function getId()
    {
        return $this->id;
    }

    /**
     * Obtiene el punto de atención
     *
     * @return PuntoAtencion
     */
    public function getPuntoAtencion()
    {
        return $this->puntoAtencion;
    }

    /**
     * Setea el punto de atención
     *
     * @param \ApiV1Bundle\Entity\PuntoAtencion $puntoAtencion
     */
    public function setPuntoAtencion($puntoAtencion)
    {
        $this->puntoAtencion = $puntoAtencion;
    }

    /**
     * Obtiene el grupo de trámite
     *
     * @return GrupoTramite
     */
    public function getGrupoTramite()
    {
        return $this->grupoTramite;
    }

    /**
     * Setea el grupo de tramites
     *
     * @param \ApiV1Bundle\Entity\GrupoTramite $grupoTramite
     */
    public function setGrupoTramite($grupoTramite)
    {
        $this->grupoTramite = $grupoTramite;
    }

    /**
     * Obtiene el horario de atención
     *
     * @return HorarioAtencion
     */
    public function getHorarioAtencion()
    {
        return $this->horarioAtencion;
    }

    /**
     * Setea el horario de atención
     *
     * @param \ApiV1Bundle\Entity\HorarioAtencion $horarioAtencion
     */
    public function setHorarioAtencion($horarioAtencion)
    {
        $this->horarioAtencion = $horarioAtencion;
    }

    /**
     * Obtiene la cantidad de turnos
     *
     * @return int
     */
    public function getCantidadTurnos()
    {
        return $this->cantidadTurnos;
    }

    /**
     * Setea la cantidad de turnos
     *
     * @param number $cantidadTurnos
     */
    public function setCantidadTurnos($cantidadTurnos)
    {
        $this->cantidadTurnos = $cantidadTurnos;
    }
}

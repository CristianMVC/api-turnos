<?php

namespace ApiV1Bundle\Entity;

use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Validator\Constraints as Assert;
use Gedmo\Mapping\Annotation as Gedmo;

/**
 * Class HorarioAtencion
 * @package ApiV1Bundle\Entity
 *
 * @ORM\Table(name="horario_atencion")
 * @ORM\Entity(repositoryClass="ApiV1Bundle\Repository\HorarioAtencionRepository")
 * @Gedmo\SoftDeleteable(fieldName="fechaBorrado")
 * @ORM\HasLifecycleCallbacks()
 */

class HorarioAtencion
{
    /**
     * Identificador único de horario de antención, autoincremental
     *
     * @var int
     *
     * @ORM\Column(name="id", type="integer")
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="AUTO")
     */
    private $id;

    /**
     * Horario de inicio de actividades para un punto de atención
     *
     * @var \DateTime
     * @Assert\NotNull(
     *     message="Este campo no puede estar vacío."
     * )
     * @Assert\Time(
     *     message="Debe ser una hora valida"
     * )
     * @ORM\Column(name="hora_inicio", type="time")
     */
    private $horaInicio;

    /**
     * Hora de cierre de actividades para un punto de atención
     *
     * @var \DateTime
     * @Assert\NotNull(
     *     message="Este campo no puede estar vacío."
     * )
     * @Assert\Time(
     *     message="Debe ser una hora valida"
     * )
     * @ORM\Column(name="hora_fin", type="time")
     */
    private $horaFin;

    /**
     * @var int
     * Representación numérica de los días de la semana siendo 1 el Lunes y 7 el Domingo
     *
     * @Assert\NotNull(
     *     message="El campo no puede estar vacío."
     * )
     * @Assert\Type(
     *     type="integer",
     *     message="Este campo solo acepta caracteres numéricos"
     * )
     * @Assert\Range(
     *     min=1,
     *     max=7
     * )
     * @ORM\Column(name="dia_semana", type="smallint")
     */
    private $diaSemana;

    /**
     * Campo para relacionar un punto de atención con un horario de atención
     * Un horario de atención pertenece a un punto de atención
     *
     * @var PuntoAtencion
     * @ORM\ManyToOne(targetEntity="PuntoAtencion", inversedBy="horariosAtencion")
     * @ORM\JoinColumn(name="puntoatencion_id", referencedColumnName="id")
     */
    private $puntoAtencion;

    /**
     * Campo para relacionar los turnos disponibles con una franja horaria de atención
     * Un horario de atención puede tener N número de turnos disponibles
     *
     * @var ArrayCollection
     * @ORM\OneToMany(targetEntity="Disponibilidad", mappedBy="horarioAtencion")
     **/
    private $turnosDisponibles;

    /**
     * ID que se utiliza en el FRONT para identificar una agrupación de Horarios de Atención
     *
     * @var integer
     * @ORM\Column(name="row_id", type="integer", nullable=false)
     */
    private $idRow;

    /**
     * Fecha de creación del horario de atención
     *
     * @var \DateTime
     * @ORM\Column(name="fecha_creado", type="datetimetz")
     */
    private $fechaCreado;

    /**
     * Fecha de modificación del horario de atención
     *
     * @var \DateTime
     * @ORM\Column(name="fecha_modificado", type="datetimetz")
     */
    private $fechaModificado;

    /**
     * Fecha de borrado
     *
     * @var \DateTime
     * @ORM\Column(name="fecha_borrado", type="datetimetz", nullable=true)
     */
    private $fechaBorrado;

    /**
     * HorarioAtencion constructor.
     *
     * @param PuntoAtencion $puntoAtencion punto de atención
     * @param integer $diaSemana Número de día de la semana en la que el horario está activo
     */
    public function __construct($puntoAtencion, $diaSemana, $horaInicio, $horaFin, $idRow)
    {
        $this->puntoAtencion = $puntoAtencion;
        $this->diaSemana = $diaSemana;
        $this->horaInicio = $horaInicio;
        $this->horaFin = $horaFin;
        $this->setIdRow($idRow);
        $this->turnosDisponibles = new ArrayCollection();
    }

    /**
     * Retorna el identificador del horario de atención
     *
     * @return integer
     */
    public function getId()
    {
        return $this->id;
    }

    /**
     * Setea una hora de inicio
     *
     * @param \DateTime $horaInicio
     * @return HorarioAtencion
     */
    public function setHoraInicio($horaInicio)
    {
        $this->horaInicio = $horaInicio;

        return $this;
    }

    /**
     * Obtiene una hora de inicio
     *
     * @return \DateTime
     */
    public function getHoraInicio()
    {
        return $this->horaInicio;
    }

    /**
     * Setea una hora de fin
     *
     * @param \DateTime $horaFin
     * @return HorarioAtencion
     */
    public function setHoraFin($horaFin)
    {
        $this->horaFin = $horaFin;

        return $this;
    }

    /**
     * Obtiene una hora fin
     *
     * @return \DateTime
     */
    public function getHoraFin()
    {
        return $this->horaFin;
    }

    /**
     * Setea un día de la semana
     *
     * @param integer $diaSemana
     * @return HorarioAtencion
     */
    public function setDiaSemana($diaSemana)
    {
        $this->diaSemana = $diaSemana;

        return $this;
    }

    /**
     * Obtiene un día de la semana
     *
     * @return integer
     */
    public function getDiaSemana()
    {
        return $this->diaSemana;
    }

    /**
     * @return int
     */
    public function getIdRow()
    {
        return $this->idRow;
    }

    /**
     * @param int $idRow
     */
    public function setIdRow($idRow)
    {
        $this->idRow = $idRow;
    }


    /**
     * Setea la fecha de creación del horario de atención
     *
     * @param \DateTime $fechaCreado
     * @return HorarioAtencion
     */
    public function setFechaCreado($fechaCreado)
    {
        $this->fechaCreado = $fechaCreado;

        return $this;
    }

    /**
     * Obtiene la fecha de creación del horario de atención
     *
     * @return \DateTime
     */
    public function getFechaCreado()
    {
        return $this->fechaCreado;
    }

    /**
     * Setea la fecha de modificación del horario de atención
     *
     * @param \DateTime $fechaModificado
     * @return HorarioAtencion
     */
    public function setFechaModificado($fechaModificado)
    {
        $this->fechaModificado = $fechaModificado;

        return $this;
    }

    /**
     * Obtiene la fecha de modificación del horario de atención
     *
     * @return \DateTime
     */
    public function getFechaModificado()
    {
        return $this->fechaModificado;
    }

    /**
     * Setea un punto de atención para un horario de atención
     *
     * @param \ApiV1Bundle\Entity\PuntoAtencion $puntoAtencion
     * @return HorarioAtencion
     */
    public function setPuntoAtencion(\ApiV1Bundle\Entity\PuntoAtencion $puntoAtencion = null)
    {
        $this->puntoAtencion = $puntoAtencion;

        return $this;
    }

    /**
     * Obtiene un punto de atención para una horario de atención
     *
     * @return \ApiV1Bundle\Entity\PuntoAtencion
     */
    public function getPuntoAtencion()
    {
        return $this->puntoAtencion;
    }

    /**
     * Genera las fechas de creación y modificación del horario de atención
     *
     * @ORM\PrePersist
     */
    public function setFechas()
    {
        $this->fechaCreado = new \DateTime();
        $this->fechaModificado = new \DateTime();
    }

    /**
     * Actualiza la fecha modificación del horario de atención
     *
     * @ORM\PreUpdate
     */
    public function updatedFechas()
    {
        $this->fechaModificado = new \DateTime();
    }

    /**
     * @return \DateTime
     */
    public function getFechaBorrado()
    {
        return $this->fechaBorrado;
    }
}

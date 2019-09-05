<?php

namespace ApiV1Bundle\Entity;

use Doctrine\ORM\Mapping as ORM;
use Doctrine\Common\Collections\ArrayCollection;
use Symfony\Component\Validator\Constraints as Assert;
use Gedmo\Mapping\Annotation as Gedmo;
use ApiV1Bundle\Entity\PuntoAtencion;
use ApiV1Bundle\Entity\Tramite;

/**
 * Class DiaNoLaborableTramite
 * @package ApiV1Bundle\Entity
 *
 * @ORM\Table(name="dias_no_laborables_tramite")
 * @ORM\Entity(repositoryClass="ApiV1Bundle\Repository\DiaNoLaborableTramiteRepository")
 * @ORM\HasLifecycleCallbacks()
 */
class DiaNoLaborableTramite
{
    /**
     * @var int
     *
     * @ORM\Column(name="id", type="integer")
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="AUTO")
     */
    private $id;

    /**
     * @var \DateTime
     *
     * @ORM\Column(name="fecha", type="date")
     */
    private $fecha;

    /**
     * Colección de días no laborables para un punto de atención.
     * @var ArrayCollection
     * @ORM\ManyToOne(targetEntity="PuntoAtencion", inversedBy="diasNoLaborablesTramite")
     * @ORM\JoinColumn(name="punto_atencion_id", referencedColumnName="id")
     */
    private $puntoAtencion;

    /**
     * Colección de días no laborables para un tramite
     * @var ArrayCollection
     * @ORM\ManyToOne(targetEntity="Tramite", inversedBy="diasNoLaborablesTramite")
     * @ORM\JoinColumn(name="tramite_id", referencedColumnName="id")
     */
    private $tramite;
    
    /**
     * @var \DateTime
     *
     * @ORM\Column(name="fecha_creado", type="datetimetz")
     */
    private $fechaCreado;

    /**
     * @var \DateTime
     *
     * @ORM\Column(name="fecha_modificado", type="datetimetz")
     */
    private $fechaModificado;

    /**
     * DiaNoLaborable constructor.
     * @param $fecha
     * @param $puntoAtencion
     */
    public function __construct($fecha, $puntoAtencion, $tramite)
    {
        $this->fecha = $fecha;
        $this->puntoAtencion = $puntoAtencion;
        $this->tramite = $tramite;
    }


    /**
     * Get id
     *
     * @return int
     */
    public function getId()
    {
        return $this->id;
    }

    /**
     * Set fecha
     *
     * @param \DateTime $fecha
     *
     * @return DiaNoLaborable
     */
    public function setFecha($fecha)
    {
        $this->fecha = $fecha;

        return $this;
    }

    /**
     * Get fecha
     *
     * @return \DateTime
     */
    public function getFecha()
    {
        return $this->fecha;
    }

    /**
     * @return mixed
     */
    public function getPuntoAtencion()
    {
        return $this->puntoAtencion;
    }

    /**
     * @param mixed $puntoAtencion
     */
    public function setPuntoAtencion($puntoAtencion)
    {
        $this->puntoAtencion = $puntoAtencion;
    }

     /**
     * @return mixed
     */
    public function getTramite()
    {
        return $this->tramite;
    }

    /**
     * @param mixed $val
     */
    public function setTramite($val)
    {
        $this->tramite = $val;
    }

    /**
     * Set fechaCreado
     *
     * @param \DateTime $fechaCreado
     *
     * @return DiaNoLaborable
     */
    public function setFechaCreado($fechaCreado)
    {
        $this->fechaCreado = $fechaCreado;
        return $this;
    }

    /**
     * Genera las fechas de creación y modificación de un punto de atención
     *
     * @ORM\PrePersist
     */
    public function setFechas()
    {
        $this->fechaCreado = new \DateTime();
        $this->fechaModificado = new \DateTime();
    }

    /**
     * Genera la fecha de modificación del punto de atención
     *
     * @ORM\PreUpdate
     */
    public function updatedFechas()
    {
        $this->fechaModificado = new \DateTime();
    }
}


<?php

namespace ApiV1Bundle\Entity;

use Doctrine\ORM\Mapping as ORM;
use Doctrine\Common\Collections\ArrayCollection;
use Symfony\Component\Validator\Constraints as Assert;
use Gedmo\Mapping\Annotation as Gedmo;
use ApiV1Bundle\Entity\PuntoAtencion;

/**
 * Class DiaNoLaborable
 * @package ApiV1Bundle\Entity
 *
 * @ORM\Table(name="dias_no_laborables")
 * @ORM\Entity(repositoryClass="ApiV1Bundle\Repository\DiaNoLaborableRepository")
 * @ORM\HasLifecycleCallbacks()
 */
class DiaNoLaborable
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
     * @ORM\ManyToOne(targetEntity="PuntoAtencion", inversedBy="diasNoLaborables")
     * @ORM\JoinColumn(name="punto_atencion_id", referencedColumnName="id")
     */
    private $puntoAtencion;

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
    public function __construct($fecha, $puntoAtencion)
    {
        $this->fecha = $fecha;
        $this->puntoAtencion = $puntoAtencion;
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


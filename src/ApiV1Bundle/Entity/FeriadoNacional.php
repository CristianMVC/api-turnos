<?php
namespace ApiV1Bundle\Entity;

use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Validator\Constraints as Assert;
use Gedmo\Mapping\Annotation as Gedmo;

/**
 * Class FeriadoNacional
 * @package ApiV1Bundle\Entity
 *
 * @ORM\Table(name="feriados_nacionales")
 * @ORM\Entity(repositoryClass="ApiV1Bundle\Repository\FeriadoNacionalRepository")
 * @ORM\HasLifecycleCallbacks()
 */

class FeriadoNacional
{
    /**
     * Identificador único de calendario, autoincremental
     *
     * @var int
     *
     * @ORM\Column(name="id", type="integer")
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="AUTO")
     *
     */
    private $id;

    /**
     * Fecha determinada de un calendario determinado
     *
     * @var Assert\Date
     *
     * @Assert\NotNull(
     *     message="La fecha no puede ser nula o vacía."
     * )
     * @ORM\Column(name="fecha", type="date")
     */
    private $fecha;


    /**
     * Fecha de creación del calendario
     *
     * @var \DateTime
     * @ORM\Column(name="fecha_creado", type="datetimetz")
     */
    private $fechaCreado;

    /**
     * Fecha de modificación del calendario
     *
     * @var \DateTime
     * @ORM\Column(name="fecha_modificado", type="datetimetz")
     */
    private $fechaModificado;

    function __construct($fecha)
    {
        $this->fecha = $fecha;
    }


    /**
     * Obtiene el Identificador único del calendario
     *
     * @return int
     */
    public function getId()
    {
        return $this->id;
    }

    /**
     * Setea una fecha en el calendario
     *
     * @param \DateTime $fecha
     * @return FeriadoNacional
     */
    public function setFecha($fecha)
    {
        $this->fecha = $fecha;

        return $this;
    }

    /**
     * Obtiene una fecha en el calendario
     *
     * @return \DateTime
     */
    public function getFecha()
    {
        return $this->fecha;
    }

    /**
     * Genera las fechas de creación y modificación del calendario
     *
     * @ORM\PrePersist
     */
    public function setFechas()
    {
        $this->fechaCreado = new \DateTime();
        $this->fechaModificado = new \DateTime();
    }

    /**
     * Actualiza la fecha de modificación del calendario
     *
     * @ORM\PreUpdate
     */
    public function updatedFechas()
    {
        $this->fechaModificado = new \DateTime();
    }
}

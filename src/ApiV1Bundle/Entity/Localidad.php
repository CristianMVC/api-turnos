<?php
namespace ApiV1Bundle\Entity;

use Doctrine\ORM\Mapping as ORM;

use Doctrine\Common\Collections\ArrayCollection;
use Symfony\Component\Validator\Constraints as Assert;

/**
 * Class Localidad
 * @package ApiV1Bundle\Entity
 *
 * @ORM\Table(name="localidad")
 * @ORM\Entity(repositoryClass="ApiV1Bundle\Repository\LocalidadRepository")
 * @ORM\HasLifecycleCallbacks()
 */

class Localidad
{
    /**
     * Identificador único de la localidad, es autoincremental
     *
     * @var int
     * @ORM\Column(name="id", type="integer")
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="AUTO")
     */
    private $id;

    /**
     * Campo para relacionar una localidad con una provincia
     * Una localidad pertenece a una sola provincia
     *
     * @var ApiV1Bundle/Entity/Provincia
     * @ORM\ManyToOne(targetEntity="Provincia", inversedBy="localidades")
     * @ORM\JoinColumn(name="provincia_id", referencedColumnName="id")
     */
    private $provincia;

    /**
     * Nombre de la localidad
     *
     * @var string
     * @Assert\NotNull(
     *     message="Este campo no puede estar vacío."
     * )
     * @Assert\Type(
     *     type="string",
     *     message="Este campo debe contener solo caracteres."
     * )
     * @ORM\Column(name="nombre", type="string", length=255)
     */
    private $nombre;

    /**
     * Fecha de creación
     *
     * @var \DateTime
     * @ORM\Column(name="fecha_creado", type="datetimetz")
     */
    private $fechaCreado;

    /**
     * Fecha de modificación
     *
     * @var \DateTime
     * @ORM\Column(name="fecha_modificado", type="datetimetz")
     */
    private $fechaModificado;

    /**
     * Coleccón de puntos de atención para poder relacionar una localidad con N puntos de atención
     * Una localidad puede tener N número de puntos de atención
     *
     * @var ArrayCollection
     * @ORM\OneToMany(targetEntity="PuntoAtencion", mappedBy="localidad", cascade={"persist"})
     */
    private $puntosAtencion;

    /**
     * Localidad constructor.
     */

    public function __construct()
    {
        $this->puntosAtencion = new ArrayCollection();
    }

    /**
     * Obtiene el Identificador único de la localidad
     *
     * @return integer
     */
    public function getId()
    {
        return $this->id;
    }

    /**
     * Setea el nombre de la localidad
     *
     * @param string $nombre
     * @return Localidad
     */
    public function setNombre($nombre)
    {
        $this->nombre = $nombre;

        return $this;
    }

    /**
     * Obtiene el nombre de la localidad
     *
     * @return string
     */
    public function getNombre()
    {
        return $this->nombre;
    }

    /**
     * Setea la fecha de creación
     *
     * @param \DateTime $fechaCreado
     * @return Localidad
     */
    public function setFechaCreado($fechaCreado)
    {
        $this->fechaCreado = $fechaCreado;

        return $this;
    }

    /**
     * Obtiene la fecha de creación
     *
     * @return \DateTime
     */
    public function getFechaCreado()
    {
        return $this->fechaCreado;
    }

    /**
     * Setea la fecha de modificación
     *
     * @param \DateTime $fechaModificado
     * @return Localidad
     */
    public function setFechaModificado($fechaModificado)
    {
        $this->fechaModificado = $fechaModificado;

        return $this;
    }

    /**
     * Obtiene la fecha de modificación
     *
     * @return \DateTime
     */
    public function getFechaModificado()
    {
        return $this->fechaModificado;
    }

    /**
     * Setea una provincia para una localidad
     *
     * @param \ApiV1Bundle\Entity\Provincia $provincia
     * @return Localidad
     */
    public function setProvincia(\ApiV1Bundle\Entity\Provincia $provincia = null)
    {
        $this->provincia = $provincia;

        return $this;
    }

    /**
     * Obtiene la provincia a la que pertenece la localidad
     *
     * @return \ApiV1Bundle\Entity\Provincia
     */
    public function getProvincia()
    {
        return $this->provincia;
    }

    /**
     * Genera las fechas de creación y modificación de la localidad
     *
     * @ORM\PrePersist
     */
    public function setFechas()
    {
        $this->fechaCreado = new \DateTime();
        $this->fechaModificado = new \DateTime();
    }

    /**
     * Modifica la fecha de modificación de la localidad
     *
     * @ORM\PreUpdate
     */
    public function updatedFechas()
    {
        $this->fechaModificado = new \DateTime();
    }
}

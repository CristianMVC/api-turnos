<?php
namespace ApiV1Bundle\Entity;

use Doctrine\ORM\Mapping as ORM;
use Doctrine\Common\Collections\ArrayCollection;
use Symfony\Component\Validator\Constraints as Assert;

/**
 * Class Provincia
 * @package ApiV1Bundle\Entity
 *
 * @ORM\Table(name="provincia")
 * @ORM\Entity(repositoryClass="ApiV1Bundle\Repository\ProvinciaRepository")
 * @ORM\HasLifecycleCallbacks()
 */

class Provincia
{
    /**
     * Identificador único para una provincia, es autoincremental
     *
     * @var int
     *
     * @ORM\Column(name="id", type="integer")
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="AUTO")
     */
    private $id;

    /**
     * Nombre de la provincia
     *
     * @var string
     * @Assert\NotNull(
     *     message="Este campo no puede estar vacío."
     * )
     * @Assert\Type(
     *     type="string",
     *     message="Este campo solo acepta carateres."
     * )
     * @ORM\Column(name="nombre", type="string", length=255, unique=true)
     */
    private $nombre;

    /**
     * Fecha de creación de la provincia
     *
     * @var \DateTime
     * @ORM\Column(name="fecha_creado", type="datetimetz")
     */
    private $fechaCreado;

    /**
     * Fecha de modificación de la provincia
     *
     * @var string
     * @ORM\Column(name="fecha_modificado", type="datetimetz")
     */
    private $fechaModificado;

    /**
     * Colección de localidades que tiene una provincia
     * Una provincia puede tener N número de localidades
     *
     * @var ArrayCollection
     * @ORM\OneToMany(targetEntity="Localidad", mappedBy="provincia", cascade={"persist"})
     */
    private $localidades;

    /**
     * Colección de puntos de atención que tiene una provincia
     * Una provincia puede tener N número de puntos de atención
     *
     * @var ArrayCollection
     * @ORM\OneToMany(targetEntity="PuntoAtencion", mappedBy="provincia", cascade={"persist"})
     */
    private $puntosAtencion;

    /**
     * Provincia constructor.
     */

    public function __construct()
    {
        $this->localidades = new ArrayCollection();
        $this->puntosAtencion = new ArrayCollection();
    }

    /**
     * Obtiene el identificador único de una provincia
     *
     * @return integer
     */
    public function getId()
    {
        return $this->id;
    }

    /**
     * Setea el nombre de una provincia
     *
     * @param string $nombre
     * @return Provincia
     */
    public function setNombre($nombre)
    {
        $this->nombre = $nombre;

        return $this;
    }

    /**
     * Obtiene el nombre de una provincia
     *
     * @return string
     */
    public function getNombre()
    {
        return $this->nombre;
    }

    /**
     * Obtiene las localidades
     *
     * @return ArrayCollection
     */
    public function getLocalidades()
    {
        return $this->localidades;
    }

    /**
     * Setea una localidad
     *
     * @param ArrayCollection $localidades
     * @return Provincia
     */
    public function setLocalidades(ArrayCollection $localidades)
    {
        $this->localidades = $localidades;
        return $this;
    }

    /**
     * Setea la fecha de creación
     *
     * @param \DateTime $fechaCreado
     * @return Provincia
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
     * @return Provincia
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
     * Genera las fechas de creación y modificación de la provincia
     *
     * @ORM\PrePersist
     */
    public function setFechas()
    {
        $this->fechaCreado = new \DateTime();
        $this->fechaModificado = new \DateTime();
    }

    /**
     * Actualiza la fecha de modificación de la provincia
     *
     * @ORM\PreUpdate
     */
    public function updatedFechas()
    {
        $this->fechaModificado = new \DateTime();
    }
}

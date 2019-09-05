<?php
namespace ApiV1Bundle\Entity;

use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Validator\Constraints as Assert;
use Doctrine\Common\Collections\ArrayCollection;
use Gedmo\Mapping\Annotation as Gedmo;

/**
 * Class Organismo
 * @package ApiV1Bundle\Entity
 *
 * @ORM\Table(name="organismo")
 * @ORM\Entity(repositoryClass="ApiV1Bundle\Repository\OrganismoRepository")
 * @Gedmo\SoftDeleteable(fieldName="fechaBorrado")
 * @ORM\HasLifecycleCallbacks()
 */

class Organismo
{
    /**
     * Identificador único del Organismo, autoincremental
     *
     * @var int
     *
     * @ORM\Column(name="id", type="integer")
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="AUTO")
     */
    private $id;

    /**
     * Nombre del organizmo
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
     * Abreviatura del organismo
     *
     * @var string
     * @Assert\Type(
     *     type="string",
     *     message="Este campo debe contener solo caracteres."
     * )
     * @ORM\Column(name="abreviatura", type="string", length=25, nullable=true)
     */
    private $abreviatura;

    /**
     * Colección de las áreas que puede tener un organismo
     * Un organismo puede tener N número de areas
     *
     * @var ArrayCollection
     * @ORM\OneToMany(targetEntity="Area", mappedBy="organismo", cascade={"persist"})
     */
    private $areas;

    /**
    * @var ArrayCollection
    * @ORM\OneToMany(targetEntity="Usuario", mappedBy="organismo", cascade={"remove"})
    */
    private $usuarios;

    /**
     * Fecha de creación del organismo
     *
     * @var \DateTime
     * @ORM\Column(name="fecha_creado", type="datetimetz")
     */
    private $fechaCreado;

    /**
     * Fecha de modificación del organismo
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
     * Organismo constructor.
     *
     * @param string $nombre Parámetro para el nombre del organismo
     * @param string $abreviatura Parámetro para la abreviatura del organismo
     */
    public function __construct($nombre, $abreviatura)
    {
        $this->setNombre($nombre);
        $this->setAbreviatura($abreviatura);
        $this->areas = new ArrayCollection();
        $this->usuarios = new ArrayCollection();
    }

    /**
     * Obtiene el identificador único de un organismo
     *
     * @return integer
     */
    public function getId()
    {
        return $this->id;
    }

    /**
     * Setea el nombre del organismo
     *
     * @param string $nombre
     * @return Organismo
     */
    public function setNombre($nombre)
    {
        $this->nombre = $nombre;

        return $this;
    }

    /**
     * Obtiene el nombre del organismo
     *
     * @return string
     */
    public function getNombre()
    {
        return $this->nombre;
    }

    /**
     * Setea la abreviatura del organismo
     *
     * @param string $abreviatura
     * @return Organismo
     */
    public function setAbreviatura($abreviatura)
    {
        $this->abreviatura = $abreviatura;

        return $this;
    }

    /**
     * Obtiene la abreviatura del organismo
     *
     * @return string
     */
    public function getAbreviatura()
    {
        return $this->abreviatura;
    }

    /**
     * Setea la fecha de creación del organismo
     *
     * @param \DateTime $fechaCreado
     * @return Organismo
     */
    public function setFechaCreado($fechaCreado)
    {
        $this->fechaCreado = $fechaCreado;

        return $this;
    }

    /**
     * Obtiene la fecha de creación del organismo
     *
     * @return \DateTime
     */
    public function getFechaCreado()
    {
        return $this->fechaCreado;
    }

    /**
     * Setea la fecha de modificación del organismo
     *
     * @param \DateTime $fechaModificado
     * @return Organismo
     */
    public function setFechaModificado($fechaModificado)
    {
        $this->fechaModificado = $fechaModificado;

        return $this;
    }

    /**
     * Obtiene la fecha de creación del organismo
     *
     * @return \DateTime
     */
    public function getFechaModificado()
    {
        return $this->fechaModificado;
    }

    /**
     * Agrega una área a un organismo
     *
     * @param \ApiV1Bundle\Entity\Area $area
     * @return Organismo
     */
    public function addArea(\ApiV1Bundle\Entity\Area $area)
    {
        $this->areas[] = $area;

        return $this;
    }

    /**
     * Remueve un área de un organismo
     *
     * @param \ApiV1Bundle\Entity\Area $area
     */
    public function removeArea(\ApiV1Bundle\Entity\Area $area)
    {
        $this->areas->removeElement($area);
    }

    /**
     * Obtiene las áreas de un organismo
     *
     * @return \Doctrine\Common\Collections\Collection
     */
    public function getAreas()
    {
        return $this->areas;
    }

    /**
     * Genera las fechas de creación y modificación de un organismo
     *
     * @ORM\PrePersist
     */
    public function setFechas()
    {
        $this->fechaCreado = new \DateTime();
        $this->fechaModificado = new \DateTime();
    }

    /**
     * Actualiza la fecha de modificación de un organismo
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

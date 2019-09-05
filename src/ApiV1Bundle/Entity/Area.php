<?php
namespace ApiV1Bundle\Entity;

use Doctrine\ORM\Mapping as ORM;
use Doctrine\Common\Collections\ArrayCollection;
use Symfony\Component\Validator\Constraints as Assert;
use Gedmo\Mapping\Annotation as Gedmo;
use Doctrine\ORM\Mapping\ManyToMany;
use Doctrine\ORM\Mapping\JoinTable;
use Doctrine\ORM\Mapping\JoinColumn;

/**
 * Class Area
 * @package ApiV1Bundle\Entity
 *
 * @ORM\Table(name="area")
 * @ORM\Entity(repositoryClass="ApiV1Bundle\Repository\AreaRepository")
 * @Gedmo\SoftDeleteable(fieldName="fechaBorrado")
 * @ORM\HasLifecycleCallbacks()
 */

class Area
{
    /**
     * Identificador único del área, es un campo autoincremental
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
     * Propiedad para referenciar a un organismo, permite relacionar una o más areas con un único organismo
     *
     * @var Organismo
     *
     * Un area pertenece a un solo organismo
     * @ORM\ManyToOne(targetEntity="Organismo", inversedBy="areas")
     * @ORM\JoinColumn(name="organismo_id", referencedColumnName="id")
     */
    private $organismo;

    /**
     * Nombre del área
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
     * Abreviatura del área
     *
     * @var string
     * @Assert\Type(
     *     type="string",
     *     message="Este campo debe contener solo caracteres"
     * )
     * @ORM\Column(name="abreviatura", type="string", length=25, nullable=true)
     */
    private $abreviatura;

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
     * Fecha de borrado
     *
     * @var \DateTime
     * @ORM\Column(name="fecha_borrado", type="datetimetz", nullable=true)
     */
    private $fechaBorrado;

    /**
     * Colección de puntos de atención para un area determinada
     *
     * @var ArrayCollection
     * Un area puede tener N número de puntos de atención
     * @ORM\OneToMany(targetEntity="PuntoAtencion", mappedBy="area", cascade={"persist"})
     **/
    private $puntosAtencion;

    /**
     * @ManyToMany(targetEntity="Tramite", mappedBy="areas")
     */
    private $tramites;

    /**
     * @var ArrayCollection
     * @ORM\OneToMany(targetEntity="Usuario", mappedBy="area", cascade={"remove"})
     */
    private $usuarios;

    /**
     * Area constructor.
     *
     * @param string $nombre Nombre del área
     * @param string $abreviatura Abreviatura del área
     */

    public function __construct($nombre, $abreviatura)
    {
        $this->setNombre($nombre);
        $this->setAbreviatura($abreviatura);
        $this->puntosAtencion = new ArrayCollection();
        $this->tramites = new ArrayCollection();
        $this->usuarios = new ArrayCollection();
    }

    /**
     * Obtiene el Identificador del área
     *
     * @return int
     */
    public function getId()
    {
        return $this->id;
    }

    /**
     * Setea el nombre del área
     *
     * @param string $nombre Nombre del área
     * @return Area
     */
    public function setNombre($nombre)
    {
        $this->nombre = $nombre;

        return $this;
    }

    /**
     * Obtiene el nombre del área
     *
     * @return string con el nombre del área
     */
    public function getNombre()
    {
        return $this->nombre;
    }

    /**
     * Setea la abreviatura del área
     *
     * @param string $abreviatura Abreviatura del área
     * @return Area
     */
    public function setAbreviatura($abreviatura)
    {
        $this->abreviatura = $abreviatura;

        return $this;
    }

    /**
     * Obtiene la abreviatura del área
     *
     * @return string
     */
    public function getAbreviatura()
    {
        return $this->abreviatura;
    }

    /**
     * Setea la fecha de Creacion
     *
     * @param \DateTime $fechaCreado
     * @return Area
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
     * @return Area
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
     * Setea el organismo al que pertenece el área
     *
     * @param \ApiV1Bundle\Entity\Organismo $organismo
     * @return Area
     */
    public function setOrganismo(Organismo $organismo = null)
    {
        $this->organismo = $organismo;

        return $this;
    }

    /**
     * Obtiene el organismo al que pertenece el área
     *
     * @return Organismo
     */
    public function getOrganismo()
    {
        return $this->organismo;
    }

    /**
     * Agrega puntos de atención
     *
     * @param \ApiV1Bundle\Entity\PuntoAtencion $puntosAtencion
     * @return Area
     */
    public function addPuntosAtencion(\ApiV1Bundle\Entity\PuntoAtencion $puntosAtencion)
    {
        $this->puntosAtencion[] = $puntosAtencion;

        return $this;
    }

    /**
     * Remueve puntos de atención
     *
     * @param \ApiV1Bundle\Entity\PuntoAtencion $puntosAtencion
     */
    public function removePuntosAtencion(\ApiV1Bundle\Entity\PuntoAtencion $puntosAtencion)
    {
        $this->puntosAtencion->removeElement($puntosAtencion);
    }

    /**
     * obtiene puntos de atención
     *
     * @return \Doctrine\Common\Collections\Collection
     */
    public function getPuntosAtencion()
    {
        return $this->puntosAtencion;
    }

    /**
     * Agrega un trámite
     *
     * @param \ApiV1Bundle\Entity\Tramite $tramite
     * @return Area
     */
    public function addTramite(\ApiV1Bundle\Entity\Tramite $tramite)
    {
        $this->getTramite()->add($tramite);

        return $this;
    }

    /**
     * Remueve un trámite
     *
     * @param \ApiV1Bundle\Entity\Tramite $tramite
     */
    public function removeTramite(\ApiV1Bundle\Entity\Tramite $tramite)
    {
        $this->tramites->removeElement($tramite);
    }

    /**
     * Obtiene una colección de trámitea
     *
     * @return \Doctrine\Common\Collections\Collection
     */
    public function getTramite() {

        return $this->tramites;
    }

    /**
     * Genera las fechas de creación y modificación del área
     *
     * @ORM\PrePersist
     */
    public function setFechas()
    {
        $this->fechaCreado = new \DateTime();
        $this->fechaModificado = new \DateTime();
    }

    /**
     * Actualiza la fecha de modificación del área
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

    /**
     * @return ArrayCollection
     */
    public function getUsuarios()
    {
        return $this->usuarios;
    }
}

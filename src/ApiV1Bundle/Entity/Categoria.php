<?php

namespace ApiV1Bundle\Entity;

use Doctrine\ORM\Mapping as ORM;
use Doctrine\Common\Collections\ArrayCollection;
use Gedmo\Mapping\Annotation as Gedmo;

/**
 * Categoria
 *
 * @ORM\Table(name="categoria")
 * @ORM\Entity(repositoryClass="ApiV1Bundle\Repository\CategoriaRepository")
 * @Gedmo\SoftDeleteable(fieldName="fechaBorrado")
 * @ORM\HasLifecycleCallbacks()
 */
class Categoria
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
     * @var string
     *
     * @ORM\Column(name="nombre", type="string", length=255)
     */
    private $nombre;

    /**
     * @var \DateTime
     *
     * @ORM\Column(name="fechaCreado", type="datetimetz")
     */
    private $fechaCreado;

    /**
     * @var \DateTime
     *
     * @ORM\Column(name="fechaModificado", type="datetimetz")
     */
    private $fechaModificado;

    /**
     * @var \DateTime
     *
     * @ORM\Column(name="fechaBorrado", type="datetimetz", nullable=true)
     */
    private $fechaBorrado;

    /**
     * Campo para referenciar un grupo de trámites con un punto de antención
     *
     * @ORM\ManyToOne(targetEntity="PuntoAtencion", inversedBy="categorias")
     * @ORM\JoinColumn(name="puntoAtencion_id", referencedColumnName="id")
     **/
    private $puntoAtencion;

    /**
     * Campo para referenciar un tramite con una categoría
     *
     * @var ArrayCollection
     * @ORM\ManyToMany(targetEntity="Tramite", inversedBy="categorias")
     * @ORM\JoinTable(name="tramites_categorias")
     **/
    private $tramites;

    /**
     * Categoria constructor.
     * @param string $nombre
     */
    public function __construct($nombre, $puntoAtencion)
    {
        $this->setNombre($nombre);
        $this->setPuntoAtencion($puntoAtencion);
        $this->tramites = new ArrayCollection();
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
     * Set nombre
     *
     * @param string $nombre
     *
     * @return Categoria
     */
    public function setNombre($nombre)
    {
        $this->nombre = $nombre;

        return $this;
    }

    /**
     * Get nombre
     *
     * @return string
     */
    public function getNombre()
    {
        return $this->nombre;
    }

    /**
     * Obtiene los tramites de una categoria
     *
     * @return mixed
     */
    public function getTramites()
    {
        return $this->tramites;
    }

    /**
     * Añade un trámite a la categoría
     *
     * @param $tramite
     */
    public function addTramite($tramite)
    {
        $this->tramites[] = $tramite;
    }

    /**
     * Borra un tramite de una categoría
     *
     * @param Tramite $tramite
     */
    public function removeTramite(Tramite $tramite)
    {
        $this->tramites->removeElement($tramite);
    }

    /**
     * Obtiene los puntos de atención para una categoria
     *
     * @return PuntoAtencion|null
     */
    public function getPuntoAtencion()
    {
        return $this->puntoAtencion;
    }

    /**
     * Setea puntos de atención para una categoria
     *
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
     * @return Categoria
     */
    public function setFechaCreado($fechaCreado)
    {
        $this->fechaCreado = $fechaCreado;

        return $this;
    }

    /**
     * Get fechaCreado
     *
     * @return \DateTime
     */
    public function getFechaCreado()
    {
        return $this->fechaCreado;
    }

    /**
     * Set fechaModificado
     *
     * @param \DateTime $fechaModificado
     *
     * @return Categoria
     */
    public function setFechaModificado($fechaModificado)
    {
        $this->fechaModificado = $fechaModificado;

        return $this;
    }

    /**
     * Get fechaModificado
     *
     * @return \DateTime
     */
    public function getFechaModificado()
    {
        return $this->fechaModificado;
    }

    /**
     * Set fechaBorrado
     *
     * @param \DateTime $fechaBorrado
     *
     * @return Categoria
     */
    public function setFechaBorrado($fechaBorrado)
    {
        $this->fechaBorrado = $fechaBorrado;

        return $this;
    }

    /**
     * Get fechaBorrado
     *
     * @return \DateTime
     */
    public function getFechaBorrado()
    {
        return $this->fechaBorrado;
    }

    /**
     * Genera las fechas de creación y modificación del grupo de trámite
     *
     * @ORM\PrePersist
     */
    public function setFechas()
    {
        $this->fechaCreado = new \DateTime();
        $this->fechaModificado = new \DateTime();
    }

    /**
     * Actualiza la fecha modificación del grupo de trámite
     *
     * @ORM\PreUpdate
     */
    public function updatedFechas()
    {
        $this->fechaModificado = new \DateTime();
    }
}

<?php
namespace ApiV1Bundle\Entity;

use Doctrine\ORM\Mapping as ORM;
use Doctrine\Common\Collections\ArrayCollection;
use Symfony\Component\Validator\Constraints as Assert;
use Gedmo\Mapping\Annotation as Gedmo;

/**
 * Class GrupoTramite
 * @package ApiV1Bundle\Entity
 *
 * @ORM\Table(name="grupo_tramite")
 * @ORM\Entity(repositoryClass="ApiV1Bundle\Repository\GrupoTramiteRepository")
 * @Gedmo\SoftDeleteable(fieldName="fechaBorrado")
 * @ORM\HasLifecycleCallbacks()
 */
class GrupoTramite
{
    /**
     * Identificador único de un grupo de trámite - Autoincremental
     *
     * @var int
     *
     * @ORM\Column(name="id", type="integer")
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="AUTO")
     */
    private $id;

    /**
     * Nombre del grupo de trámite
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
     * Vigencia del trámite
     *
     * @var int
     * @ORM\Column(name="horizonte", type="smallint", nullable=true)
     */
    private $horizonte;

    /**
     * Fecha de creación del grupo dfe trámites
     *
     * @var \DateTime
     * @ORM\Column(name="fecha_creado", type="datetimetz")
     */
    private $fechaCreado;

    /**
     * Fecha de modificación del grupo de trámites
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
     * Campo para referenciar un grupo de trámites con un punto de antención
     *
     * @ORM\ManyToOne(targetEntity="PuntoAtencion", inversedBy="grupoTramites")
     * @ORM\JoinColumn(name="puntoAtencion_id", referencedColumnName="id")
     **/
    private $puntoAtencion;

    /**
     * Campo para referenciar un trámite con un grupo de trámites
     *
     * @ORM\ManyToMany(targetEntity="Tramite", inversedBy="grupoTramites")
     * @ORM\JoinTable(name="tramites_grupotramite")
     *
     **/
    private $tramites;

    /**
     * @var float
     * @Assert\Type(
     *     type="float",
     *     message="{{ value }} solo acepta números decimales."
     * )
     * @ORM\Column(name="intervalo_tiempo", type="float", nullable=true)
     */
    private $intervaloTiempo;

    /**
     * Colección de turnos disponibles para un grupo de trámites
     * Un grupo tramite puede tener N número de turnos disponibles
     *
     * @var ArrayCollection
     * @ORM\OneToMany(targetEntity="Disponibilidad", mappedBy="grupoTramite")
     **/
    private $turnosDisponibles;

    /**
     * GrupoTramite constructor.
     *
     * @param PuntoAtencion $puntoAtencion Punto de atención del grupo de trámite
     * @param string $nombre Nombre del grupo de trámite
     * @param integer $horizonte Vigencia del trámite
     * @param float $intervalo intervalo de tiempo en minutos
     */
    public function __construct($puntoAtencion, $nombre, $horizonte, $intervalo)
    {
        $this->setPuntoAtencion($puntoAtencion);
        $this->setNombre($nombre);
        $this->setHorizonte($horizonte);
        $this->setIntervaloTiempo($intervalo);
        $this->tramites = new ArrayCollection();
        $this->turnosDisponibles = new ArrayCollection();
    }

    /**
     * Obtiene el identificador del grupo de trámites
     *
     * @return integer
     */
    public function getId()
    {
        return $this->id;
    }

    /**
     * Setea el nombre del grupo de trámites
     *
     * @param string $nombre
     * @return GrupoTramite
     */
    public function setNombre($nombre)
    {
        $this->nombre = $nombre;

        return $this;
    }

    /**
     * Obtiene el nombre del grupo de trámites
     *
     * @return string
     */
    public function getNombre()
    {
        return $this->nombre;
    }

    /**
     * Setea la vigencia del grupo de trámites
     *
     * @param integer $horizonte
     * @return GrupoTramite
     */
    public function setHorizonte($horizonte)
    {
        $this->horizonte = $horizonte;

        return $this;
    }

    /**
     * Obtiene la vigencia del grupo de trámites
     *
     * @return integer
     */
    public function getHorizonte()
    {
        return $this->horizonte;
    }

    /**
     * Obtiene los puntos de atención para un grupo de trámites
     *
     * @return mixed
     */
    public function getPuntoAtencion()
    {
        return $this->puntoAtencion;
    }

    /**
     * Setea puntos de atención para un grupo de trámites
     *
     * @param mixed $puntoAtencion
     */
    public function setPuntoAtencion($puntoAtencion)
    {
        $this->puntoAtencion = $puntoAtencion;
    }

    /**
     * Obtiene el listado de tramites del grupo
     *
     * @return $tramites
     */
    public function getTramites()
    {
        return $this->tramites;
    }

    /**
     * Agrega un trámite al grupo de trámites
     *
     * @param Tramite $tramite
     * @return GrupoTramite
     */
    public function addTramite(Tramite $tramite)
    {
        $this->tramites[] = $tramite;

        return $this;
    }

    /**
     * Borra un trámite del grupo de trámites
     *
     * @param Tramite $tramite
     */
    public function removeTramite(Tramite $tramite)
    {
        $this->tramites->removeElement($tramite);
    }

    /**
     * elimina todos los trámites asociados
     *
     */
    public function clearTramites()
    {
        $this->tramites->clear();
    }

    /**
     * Intervalo de tiempo de atención entre tramites
     *
     * @return float
     */
    public function getIntervaloTiempo()
    {
        return $this->intervaloTiempo;
    }

    /**
     * @param float $intervaloTiempo
     */
    public function setIntervaloTiempo($intervaloTiempo)
    {
        $this->intervaloTiempo = $intervaloTiempo;
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

    /**
     * @return \DateTime
     */
    public function getFechaBorrado()
    {
        return $this->fechaBorrado;
    }
}

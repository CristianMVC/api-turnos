<?php
namespace ApiV1Bundle\Entity;

use Doctrine\ORM\Mapping as ORM;
use Doctrine\Common\Collections\ArrayCollection;
use Symfony\Component\Validator\Constraints as Assert;
use Gedmo\Mapping\Annotation as Gedmo;
use JMS\Serializer\Annotation\ExclusionPolicy;
use JMS\Serializer\Annotation\Expose;

/**
 * Class PuntoAtencion
 * @package ApiV1Bundle\Entity
 * @ExclusionPolicy("all")
 * @ORM\Table(name="punto_atencion")
 * @ORM\Entity(repositoryClass="ApiV1Bundle\Repository\PuntoAtencionRepository")
 * @Gedmo\SoftDeleteable(fieldName="fechaBorrado")
 * @ORM\HasLifecycleCallbacks()
 */
class PuntoAtencion
{

    /**
     * Identificador único de un punto de atención es autoincremental
     *
     * @var int
     *
     * @ORM\Column(name="id", type="integer")
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="AUTO")
     * @Expose
     */
    private $id;

    /**
     * Campo que relaciona una punto de atención con un área
     *
     * @Assert\NotNull(
     *     message="El campo Nombre no puede estar vacío."
     * )
     * @ORM\Column(name="nombre", type="string", length=255)
     * @Expose
     */
    private $nombre;

    /**
     * Permite relacionar un punto de atención con un área
     * Un punto de atención pertenece a una sola area
     *
     * @var Area
     * @ORM\ManyToOne(targetEntity="Area", inversedBy="puntosAtencion", cascade={"persist"})
     * @ORM\JoinColumn(name="area_id", referencedColumnName="id")
     */
    private $area;

    /**
     * Campo que relaciona una provincia con un punto de atención
     * Un punto de atención pertenece a una sola provincia
     *
     * @var Provincia
     * @ORM\ManyToOne(targetEntity="Provincia", inversedBy="puntosAtencion", cascade={"persist"})
     * @ORM\JoinColumn(name="provincia_id", referencedColumnName="id")
     */
    private $provincia;

    /**
     * Campo que relaciona una localidad con un punto de atención
     * Un punto de atención pertenece a una sola localidad
     *
     * @var Localidad
     * @ORM\ManyToOne(targetEntity="Localidad", inversedBy="puntosAtencion")
     * @ORM\JoinColumn(name="localidad_id", referencedColumnName="id")
     */
    private $localidad;

    /**
     * Dirección del punto de atención
     *
     * @var string
     * @Assert\NotNull(
     *     message="Este campo no puede estar vacío."
     * )
     * @ORM\Column(name="direccion", type="string", length=255)
     */
    private $direccion;

    /**
     * Coordenada del punto de atención para ubicarlo en el mapa de la región
     *
     * @var float
     * @Assert\Type(
     *     type="float",
     *     message="El campo Latitud solo acepta números decimales."
     * )
     * @ORM\Column(name="latitud", type="float", nullable=true)
     */
    private $latitud;

    /**
     * Coordenada del punto de atención para ubicarlo en el mapa de la región
     *
     * @var float
     * @Assert\Type(
     *     type="float",
     *     message="El campo Longitud solo acepta números decimales."
     * )
     * @ORM\Column(name="longitud", type="float", nullable=true)
     */
    private $longitud;

    /**
     * Indica el estado del punto de atención
     *
     * @var int [0 => incativo, 2 => activo]
     * @Assert\NotNull(
     *     message="El campo Estado no puede estar vacío."
     * )
     * @Assert\Range(
     *     min=0,
     *     max=1
     * )
     * @ORM\Column(name="estado", type="smallint")
     */
    private $estado;

    /**
     * Colección de turnos que tiene un punto de atención
     * Un punto de atención puede tener N número de turnos
     *
     * @var ArrayCollection
     * @ORM\OneToMany(targetEntity="Turno", mappedBy="puntoAtencion", cascade={"persist"})
     **/
    private $turnos;

    /**
     * Colección de trámites para un punto de atención
     * A un punto de atención le pueden corresponde N tramites
     *
     * @var ArrayCollection
     * @ORM\OneToMany(targetEntity="PuntoTramite", mappedBy="puntoAtencion")
     */
    private $tramites;

    /**
     * Colección de grupo de trámites para un punto de atención
     * Un punto de atención puede tener N grupos de tramite
     *
     * @var ArrayCollection
     * @ORM\OneToMany(targetEntity="GrupoTramite", mappedBy="puntoAtencion")
     **/
    private $grupoTramites;

    /**
     * Colección de categorías para un punto de atención
     * Un punto de atención puede tener N categorías
     *
     * @var ArrayCollection
     * @ORM\OneToMany(targetEntity="Categoria", mappedBy="puntoAtencion")
     **/
    private $categorias;

    /**
     * Colección de horarios de atención para un punto de atención
     *Un punto de atención puede tener N número de horarios de atención
     *
     * @var ArrayCollection
     * @ORM\OneToMany(targetEntity="HorarioAtencion", mappedBy="puntoAtencion")
     **/
    private $horariosAtencion;

    /**
     * Colección de turnos disponibles para un punto de atención
     * Un punto de atención puede tener N número de turnos disponibles
     *
     * @var ArrayCollection
     * @ORM\OneToMany(targetEntity="Disponibilidad", mappedBy="puntoAtencion")
     **/
    private $turnosDisponibles;

    /**
     * Colección de días no laborables para un punto de atención.
     * @var ArrayCollection
     * @ORM\OneToMany(targetEntity="DiaNoLaborable", mappedBy="puntoAtencion", cascade={"persist"})
     */
    private $diasNoLaborables;
    
    /**
     * Colección de días no laborables para un punto de atención.
     * @var ArrayCollection
     * @ORM\OneToMany(targetEntity="DiaNoLaborableTramite", mappedBy="puntoAtencion", cascade={"persist"})
     */
    private $diasNoLaborablesTramite;

    /**
     * @var ArrayCollection
     * @ORM\OneToMany(targetEntity="Usuario", mappedBy="puntoAtencion", cascade={"remove"})
     */
    private $usuarios;

    /**
     * Fecha de creación del punto de atención
     *
     * @var \DateTime
     * @ORM\Column(name="fecha_creado", type="datetimetz")
     */
    private $fechaCreado;

    /**
     * Fecha de modificación del punto de atención
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
     * PuntoAtencion constructor.
     */
    public function __construct($nombre, $direccion)
    {
        $this->turnos = new ArrayCollection();
        $this->tramites = new ArrayCollection();
        $this->grupoTramites = new ArrayCollection();
        $this->horariosAtencion = new ArrayCollection();
        $this->turnosDisponibles = new ArrayCollection();
        $this->diasNoLaborables = new ArrayCollection();
        $this->usuarios = new ArrayCollection();

        $this->setNombre($nombre);
        $this->setDireccion($direccion);
    }

    /**
     * Obtiene el identificador de un punto de atención
     *
     * @return integer
     */
    public function getId()
    {
        return $this->id;
    }

    /**
     * Obtiene el nombre del punto de atención
     *
     * @return string
     */
    public function getNombre()
    {
        return $this->nombre;
    }

    /**
     * Setea el nombre del punto de atención
     *
     * @param string $nombre
     */
    public function setNombre($nombre)
    {
        $this->nombre = $nombre;
    }

    /**
     * Setea una dirección para un punto de atención
     *
     * @param string $direccion
     * @return PuntoAtencion
     */
    public function setDireccion($direccion)
    {
        $this->direccion = $direccion;

        return $this;
    }

    /**
     * Obtiene la dirección de un punto de atención
     *
     * @return string
     */
    public function getDireccion()
    {
        return $this->direccion;
    }

    /**
     * Setea la coordenada "Latitud" para marcar en el mapa el punto de atención
     *
     * @param float $latitud
     * @return PuntoAtencion
     */
    public function setLatitud($latitud)
    {
        $this->latitud = $latitud;

        return $this;
    }

    /**
     * Obtiene la coordenada "Latitud" para marcar en el mapa el punto de atención
     *
     * @return float
     */
    public function getLatitud()
    {
        return $this->latitud;
    }

    /**
     * Setea la coordenada "Longitud" para marcar en el mapa el punto de atención
     *
     * @param float $longitud
     * @return PuntoAtencion
     */
    public function setLongitud($longitud)
    {
        $this->longitud = $longitud;

        return $this;
    }

    /**
     * Obtiene la coordenada "Longitud" para marcar en el mapa el punto de atención
     *
     * @return float
     */
    public function getLongitud()
    {
        return $this->longitud;
    }

    /**
     * Setea la provincia a la pertenece el punto de atención
     *
     * @param string $provincia
     * @return PuntoAtencion
     */
    public function setProvincia($provincia)
    {
        $this->provincia = $provincia;

        return $this;
    }

    /**
     * Obtiene la provincia a la que pertenece el punto de atención
     *
     * @return string
     */
    public function getProvincia()
    {
        return $this->provincia;
    }

    /**
     * Obtiene el nombre de la provincia a la que pertenece
     *
     * @return string
     */
    public function getProvinciaNombre()
    {
        return $this->provincia->getNombre();
    }

    /**
     * Setea la localidad a la que pertenece el punto de atención
     *
     * @param string $localidad
     * @return PuntoAtencion
     */
    public function setLocalidad($localidad)
    {
        $this->localidad = $localidad;

        return $this;
    }

    /**
     * Obtiene la localidad a la que pertenece el punto de atención
     *
     * @return string
     */
    public function getLocalidad()
    {
        return $this->localidad;
    }

    /**
     * Obtiene el nombre de la localidad a la que pertenece
     *
     * @return string
     */
    public function getLocalidadNombre()
    {
        return $this->localidad->getNombre();
    }

    /**
     * Setea el estado del punto de atención
     *
     * @param integer $estado
     * @return PuntoAtencion
     */
    public function setEstado($estado)
    {
        $this->estado = $estado;

        return $this;
    }

    /**
     * Obtiene el estado del punto de atención
     *
     * @return integer
     */
    public function getEstado()
    {
        return $this->estado;
    }

    /**
     * Setea el área de un punto de atención
     *
     * @param \ApiV1Bundle\Entity\Area $area
     * @return PuntoAtencion
     */
    public function setArea(\ApiV1Bundle\Entity\Area $area = null)
    {
        $this->area = $area;

        return $this;
    }

    /**
     * Obtiene un área de un punto de atención
     *
     * @return \ApiV1Bundle\Entity\Area
     */
    public function getArea()
    {
        return $this->area;
    }

    /**
     * Agrega un turno a un punto de atención
     *
     * @param \ApiV1Bundle\Entity\Turno $turno
     * @return PuntoAtencion
     */
    public function addTurno(\ApiV1Bundle\Entity\Turno $turno)
    {
        $this->turnos[] = $turno;

        return $this;
    }

    /**
     * Quita un tuerno de un punto de atención
     *
     * @param \ApiV1Bundle\Entity\Turno $turno
     */
    public function removeTurno(\ApiV1Bundle\Entity\Turno $turno)
    {
        $this->turnos->removeElement($turno);
    }

    /**
     * Obtiene los turnos de un punto de atención
     *
     * @return \Doctrine\Common\Collections\Collection
     */
    public function getTurnos()
    {
        return $this->turnos;
    }

    /**
     * Agrega un trámite a un punto de atención
     *
     * @param \ApiV1Bundle\Entity\PuntoTramite $puntoTramite
     * @return PuntoAtencion
     */
    public function addTramite($puntoTramite)
    {

        $this->tramites->add($puntoTramite);

        return $this;
    }

    /**
     * Remueve un tramite de un punto de atención
     *
     * @param \ApiV1Bundle\Entity\Tramite $tramite
     */
    public function removeTramite($tramite)
    {
        $this->tramites->removeElement($tramite);
    }

    /**
     * Obtiene los tramites que pertenecen a un punto de atención
     *
     * @return \Doctrine\Common\Collections\Collection
     */
    public function getTramites()
    {
        return $this->tramites;
    }

    /**
     * Obtiene un grupo de trámites de un punto de atención
     *
     * @return ArrayCollection
     */
    public function getGrupoTramites()
    {
        return $this->grupoTramites;
    }

    /**
     * Setea un grupo de trámites para un punto de atención
     *
     * @param ArrayCollection $grupoTramites
     */
    public function setGrupoTramites($grupoTramites)
    {
        $this->grupoTramites = $grupoTramites;
    }

    /**
     * Obtiene los horarios de atencion de un punto
     *
     * @return ArrayCollection $horariosAtencion
     */
    public function getHorariosAtencion()
    {
        return $this->horariosAtencion;
    }

    /**
     * Agrega horarios de atención para un punto de atención
     *
     * @param HorarioAtencion $horarioAtencion
     * @return PuntoAtencion
     */
    public function addHorarioAtencion(HorarioAtencion $horarioAtencion)
    {
        $this->horariosAtencion[] = $horarioAtencion;

        return $this;
    }

    /**
     * Remueve horario de atencion de un punto de atención
     *
     * @param HorarioAtencion $horarioAtencion
     */
    public function removeHorarioAtencion(HorarioAtencion $horarioAtencion)
    {
        $this->horariosAtencion->removeElement($horarioAtencion);
    }

    /**
     * @return mixed
     */
    public function getDiasNoLaborables()
    {
        return $this->diasNoLaborables;
    }

    /**
     * @return mixed
     */
    public function getDiasNoLaborablesTramite()
    {
        return $this->diasNoLaborablesTramite;
    }

    /**
     * Agrega un día no laborable para un punto de atención
     *
     * @param DiaNoLaborable $diaNoLaborable
     * @return $this
     */
    public function addDiaNoLaborable(DiaNoLaborable $diaNoLaborable)
    {
        $this->diasNoLaborables[] = $diaNoLaborable;

        return $this;
    }

    /**
     * Remueve un dia no laborable de un punto de atención
     *
     * @param DiaNoLaborable $diaNoLaborable
     */
    public function removeDiaNoLaborable(DiaNoLaborable $diaNoLaborable)
    {
        $this->diasNoLaborables->removeElement($diaNoLaborable);
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

    /**
     * @return \DateTime
     */
    public function getFechaBorrado()
    {
        return $this->fechaBorrado;
    }
}

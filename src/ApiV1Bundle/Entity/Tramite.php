<?php
namespace ApiV1Bundle\Entity;

use Doctrine\ORM\Mapping as ORM;
use Doctrine\Common\Collections\ArrayCollection;
use Symfony\Component\Validator\Constraints as Assert;
use Gedmo\Mapping\Annotation as Gedmo;
use JMS\Serializer\Annotation\ExclusionPolicy;
use JMS\Serializer\Annotation\Expose;
use Doctrine\ORM\Mapping\ManyToMany;
use Doctrine\ORM\Mapping\JoinTable;
use Doctrine\ORM\Mapping\JoinColumn;


/**
 * Class Tramite
 * @package ApiV1Bundle\Entity
 * @ExclusionPolicy("all")
 * @ORM\Table(name="tramite")
 * @ORM\Entity(repositoryClass="ApiV1Bundle\Repository\TramiteRepository")
 * @Gedmo\SoftDeleteable(fieldName="fechaBorrado")
 * @ORM\HasLifecycleCallbacks()
 */

class Tramite
{
    /**
     * Identificador único del trámite, es autoincremental
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
     * @ManyToMany(targetEntity="Area", inversedBy="tramites", )
     * @JoinTable(name="tramite_area",
     *  joinColumns={
     *          @ORM\JoinColumn(name="tramite_id", referencedColumnName="id", onDelete="CASCADE")
     *     },
     *     inverseJoinColumns={
     *          @ORM\JoinColumn(name="area_id", referencedColumnName="id", onDelete="CASCADE")
     *     })
     */
    private $areas;

    /**
     * Campo para relacionar un formulario con un trámite
     *
     * @var Formulario
     * A un tramite le corresponde un solo formulario
     * @ORM\OneToOne(targetEntity="Formulario", inversedBy="tramite", cascade={"persist"})
     * @ORM\JoinColumn(name="formulario_id", referencedColumnName="id")
     */
    private $formulario;

    /**
     * Identificador Argentina Gob Ar
     *
     * @var int
     * @Assert\Type(
     *     type="integer",
     *     message="Este campo solo acepta caracteres numéricos."
     * )
     * @ORM\Column(name="argentina_gob_id", type="smallint", nullable=true)
     */
    private $idArgentinaGobAr;

    /**
     * Nombre del trámite
     *
     * @var string
     * @Assert\NotNull(
     *     message="Este campo no puede estar vacío"
     * )
     * @Assert\Type(
     *     type="string",
     *     message="Este campo solo acepta caracteres."
     * )
     * @ORM\Column(name="nombre", type="string", length=255)
     * @Expose
     */
    private $nombre;

    /**
     * descripcion del trámite
     *
     * @var string
     * @Assert\Type(
     *     type="string",
     *     message="Este campo solo acepta caracteres."
     * )
     * @ORM\Column(name="descripcion", type="string", length=140)
     * @Expose
     */
    private $descripcion;
    
    /**
     * Duración del trámite
     *
     * @var int
     * @Assert\Type(
     *     type="integer",
     *     message="Este campo solo acepta caracteres numéricos."
     * )
     * @ORM\Column(name="duracion", type="smallint", nullable=true)
     */
    private $duracion;

    /**
     * Requisitos del trámite
     *
     * @var string
     * @ORM\Column(name="requisitos", type="text", nullable=true)
     */
    private $requisitos;

    /**
     * Visibilidad del trámite
     *
     * @var int
     * @Assert\NotNull(
     *     message="Este campo no puede estar vacío."
     * )
     * @Assert\Type(
     *     type="integer",
     *     message="Este campo no puede estar vacío y solo acepta caracteres numéricos."
     * )
     * @ORM\Column(name="visibilidad", type="smallint")
     */
    private $visibilidad;

    /**
     * Colección de turnos que tiene un trámite
     * Un tramite puede tener N número de turnos
     *
     * @var ArrayCollection
     * @ORM\OneToMany(targetEntity="Turno", mappedBy="tramite")
     **/
    private $turnos;

    /**
     * Colección de puntos de atención a los que pertenece el trámite
     * Un tramite puede pertenecer a N puntos de atención
     *
     * @var ArrayCollection
     * @ORM\OneToMany(targetEntity="PuntoTramite", mappedBy="tramite")
     */
    private $puntosAtencion;

    /**
     * Campo para relacionar un trámite con un grupo de trámites
     *
     * @ORM\ManyToMany(targetEntity="GrupoTramite", mappedBy="tramites")
     *
     **/
    private $grupoTramites;

    /**
     * Campo para relacionar un trámite con una categoria
     *
     * @ORM\ManyToMany(targetEntity="Categoria", mappedBy="tramites")
     **/
    private $categorias;

    /**
     *@var ArrayCollection
     * Campo para relacionar un trámite con una etiqueta
     * @ORM\ManyToMany(targetEntity="Etiqueta", inversedBy="tramites")
     * @ORM\JoinTable(name="etiquetas_tramites")
     * @ORM\JoinColumn(nullable=true)
     * @ORM\ManyToMany(targetEntity="Etiqueta", mappedBy="tramites")
     **/
    private $etiquetas;

    /**
     * @ORM\ManyToOne(targetEntity="CategoriaTramite", inversedBy="tramite")
     */
    private $categoriaTramite;


    /**
     * Es un trámite excepcional
     *
     * @var int
     * @Assert\NotNull(
     *     message="Este campo no puede estar vacío."
     * )
     * @Assert\Type(
     *     type="integer",
     *     message="Este campo no puede estar vacío y solo acepta caracteres numéricos."
     * )
     * @ORM\Column(name="excepcional", type="smallint")
     * @Expose
     */
    private $excepcional;

    /**
     * Fecha de creación del trámite
     *
     * @var \DateTime
     * @ORM\Column(name="fecha_creado", type="datetimetz")
     */
    private $fechaCreado;

    /**
     * Fecha de modificación del trámite
     *
     * @var \DateTime
     * @ORM\Column(name="fecha_modificado", type="datetimetz")
     */
    private $fechaModificado;

    /**
     * Fecha de borrado del organismo
     *
     * @var \DateTime
     * @ORM\Column(name="fecha_borrado", type="datetimetz", nullable=true)
     */
    private $fechaBorrado;

    /**
     * Tramite constructor.
     *
     * @param String $nombre Nombre del trámite
     * @param integer $visibilidad
     * @param objeto Area $area
     */

    /**
     * @var boolean
     * @ORM\Column(name="org", type="boolean", nullable=false)
     */
    private $org;


    /**
     * @var boolean
     * @ORM\Column(name="mi_argentina", type="boolean", nullable=false)
     */
    private $miArgentina;




    /**
     * Colección de días no laborables para un punto de atención.
     * @var ArrayCollection
     * @ORM\OneToMany(targetEntity="DiaNoLaborableTramite", mappedBy="tramite", cascade={"persist"})
     */
    private $diasNoLaborablesTramite;

    public function __construct($nombre, $visibilidad)
    {
        $this->setNombre($nombre);
        $this->setVisibilidad($visibilidad);

        $this->areas = new ArrayCollection();
        $this->turnos = new ArrayCollection();
        $this->puntosAtencion = new ArrayCollection();
        $this->grupoTramites = new ArrayCollection();
        $this->etiquetas = new ArrayCollection();
    }



    /**
     * Obtiene el identificador único del trámite
     *
     * @return integer
     */
    public function getId()
    {
        return $this->id;
    }

    /**
     * Setea el identificador de Argentina Gob Ar
     *
     * @param integer $idArgentinaGobAr
     * @return Tramite
     */
    public function setIdArgentinaGobAr($idArgentinaGobAr)
    {
        $this->idArgentinaGobAr = $idArgentinaGobAr;

        return $this;
    }

    /**
     * Obtiene le identificador de Argentina Gob ar
     *
     * @return integer
     */
    public function getIdArgentinaGobAr()
    {
        return $this->idArgentinaGobAr;
    }

    /**
     * Setea el nombre del trámite
     *
     * @param string $nombre
     * @return Tramite
     */
    public function setNombre($nombre)
    {
        $this->nombre = $nombre;

        return $this;
    }

    /**
     * Obtiene el nombre del trámite
     *
     * @return string
     */
    public function getNombre()
    {
        return $this->nombre;
    }

    
    
     /**
     * Setea la descripcion del trámite
     *
     * @param string $descripcion
     * @return Tramite
     */
    public function setDescripcion($descripcion)
    {
        $this->descripcion = $descripcion;

        return $this;
    }

    /**
     * Obtiene la descripcion del trámite
     *
     * @return string
     */
    public function getDescripcion()
    {
        return $this->descripcion;
    }
    /**
     * Setea la duración del trámite
     *
     * @param integer $duracion
     * @return Tramite
     */
    public function setDuracion($duracion)
    {
        $this->duracion = $duracion;

        return $this;
    }

    /**
     * Obtiene la duración del trámite
     *
     * @return integer
     */
    public function getDuracion()
    {
        return $this->duracion;
    }

    /**
     * Setea los requisitos del trámite
     *
     * @param string $requisitos
     * @return Tramite
     */
    public function setRequisitos($requisitos)
    {
        $this->requisitos = $requisitos;

        return $this;
    }

    /**
     * Obtiene los requisitos del trámite
     *
     * @return string
     */
    public function getRequisitos()
    {
        return $this->requisitos;
    }

    /**
     * Setea si es visible o no el trámite
     *
     * @param integer $visibilidad
     * @return Tramite
     */
    public function setVisibilidad($visibilidad)
    {
        $this->visibilidad = $visibilidad;

        return $this;
    }

    /**
     * Obtiene la visibilidad del trámite
     *
     * @return integer
     */
    public function getVisibilidad()
    {
        return $this->visibilidad;
    }

    /**
     * Obtiene si el trámite es excepcional
     *
     * @return integer
     */
    public function getExcepcional()
    {
        return $this->excepcional;
    }

    /**
     * Setea si el trámite es excepcional
     *
     * @param integer $excepcional
     * @return Tramite
     */
    public function setExcepcional($excepcional)
    {
        $this->excepcional = $excepcional;

        return $this;
    }

    /**
     * Setea la fecha de creación del trámite
     *
     * @param \DateTime $fechaCreado
     * @return Tramite
     */
    public function setFechaCreado($fechaCreado)
    {
        $this->fechaCreado = $fechaCreado;

        return $this;
    }

    /**
     * Obtiene la fecha de creación del trámite
     *
     * @return \DateTime
     */
    public function getFechaCreado()
    {
        return $this->fechaCreado;
    }

    /**
     * Setea la fecha de modificación del trámite
     *
     * @param \DateTime $fechaModificado
     * @return Tramite
     */
    public function setFechaModificado($fechaModificado)
    {
        $this->fechaModificado = $fechaModificado;

        return $this;
    }

    /**
     * Obtiene la fecha de modificación del trámite
     *
     * @return \DateTime
     */
    public function getFechaModificado()
    {
        return $this->fechaModificado;
    }

    public function addArea($areas)
    {
        foreach($areas as $a) {
            $this->getArea()->add($a);
        }
        //$this->getArea()->add($area);
    }


    public function removeArea($areas) {
        foreach($areas as $a) {
            $this->getArea()->removeElement($a);
        }

    }


    /**
     * @return Collection
     */
    public function getArea() {
        return $this->areas;
    }




    /**
     * Setea el formulario para el trámite
     *
     * @param \ApiV1Bundle\Entity\Formulario $formulario
     * @return Tramite
     */
    public function setFormulario(\ApiV1Bundle\Entity\Formulario $formulario = null)
    {
        $this->formulario = $formulario;

        return $this;
    }

    /**
     * Obtiene el formulario para el trámite
     *
     * @return \ApiV1Bundle\Entity\Formulario
     */
    public function getFormulario()
    {
        return $this->formulario;
    }

    /**
     * Agrega un turno para un trámite
     *
     * @param \ApiV1Bundle\Entity\Turno $turno
     * @return Tramite
     */
    public function addTurno(\ApiV1Bundle\Entity\Turno $turno)
    {
        $this->turnos[] = $turno;

        return $this;
    }

    /**
     * Remueve un turno para un trámite
     *
     * @param \ApiV1Bundle\Entity\Turno $turno
     */
    public function removeTurno(\ApiV1Bundle\Entity\Turno $turno)
    {
        $this->turnos->removeElement($turno);
    }

    /**
     * Obtiene los turnos de un trámite
     *
     * @return \Doctrine\Common\Collections\Collection
     */
    public function getTurnos()
    {
        return $this->turnos;
    }

    /**
     * Agrega puntos de atención para un trámite
     *
     * @param \ApiV1Bundle\Entity\PuntoAtencion $puntosAtencion
     * @return Tramite
     */
    public function addPuntosAtencion(\ApiV1Bundle\Entity\PuntoAtencion $puntosAtencion)
    {
        $this->puntosAtencion[] = $puntosAtencion;

        return $this;
    }

    /**
     * Remueve puntos de atención para un trámite
     *
     * @param \ApiV1Bundle\Entity\PuntoAtencion $puntosAtencion
     */
    public function removePuntosAtencion(\ApiV1Bundle\Entity\PuntoAtencion $puntosAtencion)
    {
        $this->puntosAtencion->removeElement($puntosAtencion);
    }

    /**
     * Obtiene los puntos de atención para un trámite
     *
     * @return \Doctrine\Common\Collections\Collection
     */
    public function getPuntosAtencion()
    {
        return $this->puntosAtencion;
    }

    /**
     * Obtiene los grupos para un trámite
     *
     * @return mixed
     */
    public function getGrupoTramites()
    {
        return $this->grupoTramites;
    }

    /**
     * Obtiene las categorías para un trámite
     *
     * @return mixed
     */
    public function getCategorias()
    {
        return $this->categorias;
    }


    public function addEtiquetas($etiquetas) {
        $this->etiquetas = $etiquetas;
    }



    /**
     * Obtiene las etiquetas para un trámite
     *
     * @return mixed
     */
    public function getEtiquetas()
    {
        return $this->etiquetas;
    }

    /**
     * Obtiene el Grupo del Tramite de un Punto de atención
     */
    public function getGrupoTramiteByPunto($puntoAtencion)
    {
        $gruposFilter = $this->getGrupoTramites()->filter(function ($grupoTramite) use ($puntoAtencion){
            return $grupoTramite->getPuntoAtencion()->getId() == $puntoAtencion->getId();
        });

       return $gruposFilter->first();
    }

    /**
     * Genera las fechas de creación y modificación de un trámite
     *
     * @ORM\PrePersist
     */
    public function setFechas()
    {
        $this->fechaCreado = new \DateTime();
        $this->fechaModificado = new \DateTime();
    }

    /**
     * Actualiza la fecha de modificación de un trámite
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



    public function setOrg($org) {

        $this->org = $org;

    }


    public function getOrg() {

        return $this->org;

    }

    /**
     * @return mixed
     */
    public function getCategoriaTramite()
    {
        return $this->categoriaTramite;
    }

    /**
     * @param mixed $categoriaTramite
     */
    public function setCategoriaTramite($categoriaTramite)
    {
        $this->categoriaTramite = $categoriaTramite;
        return $this;
    }


    /**
     * @return mixed
     */
    public function getDiasNoLaborablesTramite()
    {
        return $this->diasNoLaborablesTramite;
    }



    public function setMiArgentina($miArgentina) {

        $this->miArgentina = $miArgentina;

    }


    public function getMiArgentina() {

        return $this->miArgentina;

    }



}

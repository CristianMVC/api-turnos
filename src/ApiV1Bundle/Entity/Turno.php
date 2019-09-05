<?php
namespace ApiV1Bundle\Entity;

use Doctrine\ORM\Mapping as ORM;
use Ramsey\Uuid\Uuid;
use Symfony\Component\Validator\Constraints as Assert;
use Gedmo\Mapping\Annotation as Gedmo;
use JMS\Serializer\Annotation\ExclusionPolicy;
use JMS\Serializer\Annotation\Expose;
use JMS\Serializer\Annotation\SerializedName;
use JMS\Serializer\Annotation\VirtualProperty;
use JMS\Serializer\Annotation\Inline;

/**
 * Class Turno
 * @package ApiV1Bundle\Entity
 * @ExclusionPolicy("all")
 * @VirtualProperty(
 *     "fechaFormateada",
 *     exp="object.getFechaFormateada()",
 *     options={@SerializedName("fecha")}
 * )
 * @VirtualProperty(
 *     "horaFormateada",
 *     exp="object.getHoraFormateada()",
 *     options={@SerializedName("hora")}
 * )
 * @ORM\Table(name="turno")
 * @ORM\Entity(repositoryClass="ApiV1Bundle\Repository\TurnoRepository")
 * @Gedmo\SoftDeleteable(fieldName="fechaBorrado")
 * @ORM\HasLifecycleCallbacks()
 */

class Turno
{

    const ESTADO_RESERVADO = 0;
    const ESTADO_ASIGNADO = 1;
    const ESTADO_CANCELADO = 2;
    
    const ORIGEN_BACKEND = 1;
    const ORIGEN_MIARGENTINA = 2;

    /**
     * Identificador único del turno, autoincremental
     *
     * @var int
     * @ORM\Column(name="id", type="integer")
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="AUTO")
     * @Expose
     */
    private $id;

    /**
     * Campo de relación con el punto de atención al que pertenece el turno
     * Un turno pertenece a un solo punto de atención
     *
     * @var PuntoAtencion
     * @ORM\ManyToOne(targetEntity="PuntoAtencion", inversedBy="turnos")
     * @ORM\JoinColumn(name="punto_atencion_id", referencedColumnName="id")
     * @Expose
     */
    private $puntoAtencion;

    /**
     * Campo de relación con el trámite al que pertenece el turno
     * Un turno puede pertenecer a un solo tramite
     *
     * @var Tramite
     * @ORM\ManyToOne(targetEntity="Tramite", inversedBy="turnos")
     * @ORM\JoinColumn(name="tramite_id", referencedColumnName="id")
     * @Expose
     */
    private $tramite;

    /**
     * Campo de relación con el grupo tramite al que pertenece el turno
     * Un turno puede pertenecer a un solo grupo tramite
     *
     * @var GrupoTramite
     * @ORM\ManyToOne(targetEntity="GrupoTramite")
     * @ORM\JoinColumn(name="grupo_tramite_id", referencedColumnName="id")
     */
    private $grupoTramite;

    /**
     * Campo de relación con los datos del turno
     * A un turno le corresponde un solo grupo de datos
     *
     * @var DatosTurno
     * @ORM\OneToOne(targetEntity="DatosTurno", inversedBy="turno", cascade={"persist"})
     * @ORM\JoinColumn(name="datos_turno_id", referencedColumnName="id")
     * @Expose
     * @Inline
     */
    private $datosTurno;

    /**
     * Clave hash de cada turno
     *
     * @var string
     * @Assert\NotNull(
     *     message="Este campo no puede estar vacío."
     * )
     * @ORM\Column(name="codigo", type="string", unique=true, length=64)
     * @Expose
     */
    private $codigo;

    /**
     * Fecha del turno
     *
     * @var \DateTime
     *
     * @Assert\DateTime()
     * @ORM\Column(name="fecha", type="date")
     */
    private $fecha;

    /**
     * Hora del turno
     *
     * @var \DateTime
     *
     * @Assert\DateTime()
     * @ORM\Column(name="hora", type="time")
     */
    private $hora;

    /**
     * Estados que puede tener el turno:
     * [0 => reservado, 1 => asignado, 2 => cancelado]
     *
     * @var int
     * @Assert\Type(
     *     type="integer",
     *     message="Este campo no puede estar vacío y debe ser numérico."
     * )
     * @Assert\Range(min = 0, max = 2)
     * @ORM\Column(name="estado", type="smallint")
     * @Expose
     */
    private $estado;

    /**
     * Tipo de alerta, 1 si es por email y 2 si es por sms:
     * [1 => email, 2 => sms]
     *
     * @var int
     * @Assert\Range(
     *     min=1,
     *     max=2
     * )
     * @ORM\Column(name="alerta", type="smallint", nullable=true)
     * @Expose
     */
    private $alerta;

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
     * Fecha de eliminación
     *
     * @var \DateTime
     * @ORM\Column(name="fecha_borrado", type="datetimetz", nullable=true)
     */
    private $fechaBorrado;

    
    /**
     * Campo de relación con el usuario creador del turno
     * Un turno pertenece a un solo usuario creador
     *
     * @var user
     * @ORM\ManyToOne(targetEntity="User", inversedBy="turnos")
     * @ORM\JoinColumn(name="user", referencedColumnName="id", nullable=true)
     * @Expose
     */
    private $user;
    
        /**
     * Tipo de origin, 1 si es por backoffice, es por Argentina, 3 por la app :
     * [1 => backoffice, 2 => Argentina]
     *
     * @var int
     * @Assert\Range(
     *     min=1,
     *     max=2
     * )
     * @ORM\Column(name="origen", type="smallint", nullable=true)
     * @Expose
     */
    private $origen;
    
    
   /** @var string
     * @ORM\Column(name="cuil_solicitante", type="string", length=50)
     */
    private $cuilSolicitante;
    
    
    
    /**
     * Turno constructor.
     *
     * Turno constructor.
     * @param $puntoAtencion
     * @param $tramite
     * @param $grupoTramite
     * @param $fecha
     * @param $hora
     */
    public function __construct(PuntoAtencion $puntoAtencion, Tramite $tramite, $fecha, $hora, $user= null, $origen= null)
    {
        $this->puntoAtencion = $puntoAtencion;
        $this->tramite = $tramite;
        $this->grupoTramite = $tramite->getGrupoTramiteByPunto($puntoAtencion);
        $this->fecha = $fecha;
        $this->hora = $hora;
        $this->estado = $this::ESTADO_RESERVADO;
        $this->setUser($user);
        $this->setOrigen($origen);
        $this->setCodigo();
    }

    /**
     * Obtiene el identificador único de turno
     *
     * Get id
     *
     * @return integer
     */
    public function getId()
    {
        return $this->id;
    }

    /**
     * Setea el punto de atención al que el turno pertenece
     *
     * Set puntoAtencionId
     *
     * @param integer $puntoAtencionId
     * @return Turno
     */
    public function setPuntoAtencionId($puntoAtencionId)
    {
        $this->puntoAtencionId = $puntoAtencionId;

        return $this;
    }

    /**
     * Obtiene el punto de atención
     *
     * Get puntoAtencionId
     *
     * @return integer
     */
    public function getPuntoAtencionId()
    {
        return $this->puntoAtencionId;
    }

    /**
     * Setea el identificador del trámite asociado al turno
     *
     * Set tramiteId
     *
     * @param integer $tramiteId
     * @return Turno
     */
    public function setTramiteId($tramiteId)
    {
        $this->tramiteId = $tramiteId;

        return $this;
    }

    /**
     * Obtiene el identificador del trámite asociado al turno
     *
     * Get tramiteId
     * @return integer
     */
    public function getTramiteId()
    {
        return $this->tramiteId;
    }

    /**
     * Setea una clave hash para cada turno
     *
     * Set codigo
     * @return Turno
     */
    public function setCodigo()
    {
        $this->codigo = $this->generateUniqueId();

        return $this;
    }

    /**
     * Obitiene la clave hash para cada turno
     *
     * Get codigo
     *
     * @return string
     */
    public function getCodigo()
    {
        return $this->codigo;
    }

    /**
     * Setea la fecha del turno
     *
     * @param \DateTime $fecha
     * @return Turno
     */
    public function setFecha($fecha)
    {
        $this->fecha = $fecha;

        return $this;
    }

    /**
     * obtiene la fecha del turno
     *
     * Get fecha
     *
     * @return \DateTime
     */
    public function getFecha()
    {
        return $this->fecha;
    }

    /**
     * Setea la hora del turno
     *
     * @param \DateTime $hora
     * @return Turno
     */
    public function setHora($hora)
    {
        $this->hora = $hora;

        return $this;
    }

    /**
     * Obtiene la hora del turno
     *
     * @return \DateTime
     */
    public function getHora()
    {
        return $this->hora;
    }

    /**
     * Setea el estado del turno
     *
     * @param integer $estado
     * @return Turno
     */
    public function setEstado($estado)
    {
        $this->estado = $estado;

        return $this;
    }

    /**
     * Obtiene el estado del turno
     *
     * @return integer
     */
    public function getEstado()
    {
        return $this->estado;
    }

    /**
     * Setea el alerta del turno
     *
     * @param integer $alerta
     * @return Turno
     */
    public function setAlerta($alerta)
    {
        $this->alerta = $alerta;

        return $this;
    }

    /**
     * Obtiene el alerta del turno
     *
     * @return integer
     */
    public function getAlerta()
    {
        return $this->alerta;
    }

    /**
     * Setea la fecha de creación del turno
     *
     * @param \DateTime $fechaCreado
     * @return Turno
     */
    public function setFechaCreado($fechaCreado)
    {
        $this->fechaCreado = $fechaCreado;

        return $this;
    }

    /**
     * Obtiene la fecha de creación del turno
     *
     * @return \DateTime
     */
    public function getFechaCreado()
    {
        return $this->fechaCreado;
    }

    /**
     * Setea la fecha de modificación del turno
     *
     * @param \DateTime $fechaModificado
     * @return Turno
     */
    public function setFechaModificado($fechaModificado)
    {
        $this->fechaModificado = $fechaModificado;

        return $this;
    }

    /**
     * Obtiene la fecha de modificación del turno
     *
     * @return \DateTime
     */
    public function getFechaModificado()
    {
        return $this->fechaModificado;
    }

    /**
     * Setea el punto de atención al que pertenece el turno
     *
     * @param \ApiV1Bundle\Entity\PuntoAtencion $puntoAtencion
     * @return Turno
     */
    public function setPuntoAtencion(\ApiV1Bundle\Entity\PuntoAtencion $puntoAtencion = null)
    {
        $this->puntoAtencion = $puntoAtencion;

        return $this;
    }

    /**
     * Obtiene el punto de atención al que pertenece el turno
     *
     * @return \ApiV1Bundle\Entity\PuntoAtencion
     */
    public function getPuntoAtencion()
    {
        return $this->puntoAtencion;
    }

    /**
     * Setea el trámite al que pertenece el turno
     *
     * @param \ApiV1Bundle\Entity\Tramite $tramite
     * @return Turno
     */
    public function setTramite(\ApiV1Bundle\Entity\Tramite $tramite = null)
    {
        $this->tramite = $tramite;

        return $this;
    }

    /**
     * Obtiene el trámite al que pertenece el turno
     *
     * @return \ApiV1Bundle\Entity\Tramite
     */
    public function getTramite()
    {
        return $this->tramite;
    }

    /**
     * Setea los datos del turno
     *
     * @param \ApiV1Bundle\Entity\DatosTurno $datosTurno
     * @return Turno
     */
    public function setDatosTurno(\ApiV1Bundle\Entity\DatosTurno $datosTurno = null)
    {
        $this->datosTurno = $datosTurno;

        return $this;
    }

    /**
     * Obtiene los datos del turno
     *
     * @return \ApiV1Bundle\Entity\DatosTurno
     */
    public function getDatosTurno()
    {
        return $this->datosTurno;
    }

    /**
     * @return GrupoTramite
     */
    public function getGrupoTramite()
    {
        return $this->grupoTramite;
    }

    /**
     * Retorna un nuevo Uuid
     *
     * @return string
     */

    private function generateUniqueId()
    {
        return Uuid::uuid4();
    }

    /**
     * Genera las fechas de creación y modificación de un turno
     *
     * @ORM\PrePersist
     */
    public function setFechas()
    {
        $this->fechaCreado = new \DateTime();
        $this->fechaModificado = new \DateTime();
    }

    /**
     * Actualiza la fecha de modificación de un turno
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
     * Getter para propiedad virtual fechaFormateada
     * @return string
     */
    public function getFechaFormateada()
    {
        return date_format($this->fecha, 'Y-m-d');
    }
    
    /**
     * Getter para propiedad virtual horaFormateada
     * @return string
     */
    public function getHoraFormateada()
    {
        return date_format($this->hora, 'H:i');
    }
    


    /**
     * Set origen
     *
     * @param integer $origen
     *
     * @return Turno
     */
    public function setOrigen($origen)
    {
        $this->origen = $origen;

        return $this;
    }

    /**
     * Get origen
     *
     * @return integer
     */
    public function getOrigen()
    {
        return $this->origen;
    }

    /**
     * Set grupoTramite
     *
     * @param \ApiV1Bundle\Entity\GrupoTramite $grupoTramite
     *
     * @return Turno
     */
    public function setGrupoTramite(\ApiV1Bundle\Entity\GrupoTramite $grupoTramite = null)
    {
        $this->grupoTramite = $grupoTramite;

        return $this;
    }

    /**
     * Set user
     *
     * @param \ApiV1Bundle\Entity\User $user
     *
     * @return Turno
     */
    public function setUser(\ApiV1Bundle\Entity\User $user = null)
    {
        $this->user = $user;

        return $this;
    }

    /**
     * Get user
     *
     * @return \ApiV1Bundle\Entity\User
     */
    public function getUser()
    {
        return $this->user;
    }
    
     /**
     * Setea el cuit solicitante
     *
     * @param string $cuilSolicitante
     * @return Turno
     */
    public function setCuil($cuil) {
        $this->cuilSolicitante = $cuil;
        return $this;
    }
    
    
    /**
     * Obtiene el cuit solicitante
     *
     * @return string $cuilSolicitante
     */
    public function getCuil() {
        return $this->cuilSolicitante;
        
    }
    
}

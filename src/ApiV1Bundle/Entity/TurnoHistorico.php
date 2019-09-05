<?php
namespace ApiV1Bundle\Entity;

use Doctrine\ORM\Mapping as ORM;
use Ramsey\Uuid\Uuid;
use Symfony\Component\Validator\Constraints as Assert;
use Gedmo\Mapping\Annotation as Gedmo;

/**
 * Class Turno
 * @package ApiV1Bundle\Entity
 *
 * @ORM\Table(name="turno_historico")
 * @ORM\Entity(repositoryClass="ApiV1Bundle\Repository\TurnoHistoricoRepository")
 * @Gedmo\SoftDeleteable(fieldName="fechaBorrado")
 * @ORM\HasLifecycleCallbacks()
 */

class TurnoHistorico
{

    const ESTADO_RESERVADO = 0;
    const ESTADO_ASIGNADO = 1;
    const ESTADO_CANCELADO = 2;

    /**
     * Identificador único del turno, autoincremental
     *
     * @var int
     * @ORM\Column(name="id", type="integer")
     * @ORM\Id
     */
    private $id;

    /**
     * Campo de relación con el punto de atención al que pertenece el turno
     * Un turno pertenece a un solo punto de atención
     *
     * @var PuntoAtencion
     * @ORM\ManyToOne(targetEntity="PuntoAtencion", inversedBy="turnos")
     * @ORM\JoinColumn(name="punto_atencion_id", referencedColumnName="id")
     */
    private $puntoAtencion;

    /**
     * Campo de relación con el trámite al que pertenece el turno
     * Un turno puede pertenecer a un solo tramite
     *
     * @var Tramite
     * @ORM\ManyToOne(targetEntity="Tramite", inversedBy="turnos")
     * @ORM\JoinColumn(name="tramite_id", referencedColumnName="id")
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
     * @ORM\OneToOne(targetEntity="DatosTurnoHistorico", inversedBy="turno", cascade={"persist"})
     * @ORM\JoinColumn(name="datos_turno_historico_id", referencedColumnName="id")
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
     * TurnoHistorico constructor.
     *
     * @param Turno $turno
     */
    public function __construct(Turno $turno)
    {
        $this->id = $turno->getId();
        $this->codigo = $turno->getCodigo();
        $this->tramite = $turno->getTramite();
        $this->grupoTramite = $turno->getGrupoTramite();
        $this->puntoAtencion = $turno->getPuntoAtencion();
        $this->estado = $turno->getEstado();
        $this->fecha = $turno->getFecha();
        $this->hora = $turno->getHora();
        $this->fechaCreado = $turno->getFechaCreado();
        $this->fechaModificado = $turno->getFechaModificado();
        $this->fechaBorrado= $turno->getFechaBorrado();
    }

    /**
     * Setea los datos del turno
     *
     * @param DatosTurnoHistorico $datosTurno
     * @return TurnoHistorico
     */
    public function setDatosTurno(DatosTurnoHistorico $datosTurno = null)
    {
        $this->datosTurno = $datosTurno;

        return $this;
    }
}

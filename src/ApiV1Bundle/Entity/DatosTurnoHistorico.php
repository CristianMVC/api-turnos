<?php
namespace ApiV1Bundle\Entity;

use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Validator\Constraints as Assert;
use Gedmo\Mapping\Annotation as Gedmo;

/**
 * Class DatosTurno
 * @package ApiV1Bundle\Entity
 *
 * DatosTramite
 *
 * @ORM\Table(name="datos_turno_historico")
 * @ORM\Entity(repositoryClass="ApiV1Bundle\Repository\DatosTurnoHistoricoRepository")
 * @Gedmo\SoftDeleteable(fieldName="fechaBorrado")
 * @ORM\HasLifecycleCallbacks()
 */

class DatosTurnoHistorico
{
    /**
     * Identificador único de datos de un turno, autoincremental
     *
     * @var int
     *
     * @ORM\Column(name="id", type="integer")
     * @ORM\Id
     */
    private $id;

    /**
     * Propiedad para la relación entre los datos del turno y el turno
     * Los datos del tramite le corresponden a un solo turno
     *
     * @var Turno
     * @ORM\OneToOne(targetEntity="TurnoHistorico", mappedBy="datosTurno",  cascade={"persist"})
     */
    private $turno;

    /**
     * Nombre del ciudadano
     *
     * @var string
     * @Assert\NotNull(
     *     message="Este campo no puede estar vacío."
     * )
     * @Assert\Email(
     *     message = "El nombre del ciudadano es obligatorio.",
     * )
     * @ORM\Column(name="nombre", type="string", length=80)
     */
    private $nombre;

    /**
     * Apellido del ciudadano
     *
     * @var string
     * @Assert\NotNull(
     *     message="Este campo no puede estar vacío."
     * )
     * @Assert\Email(
     *     message = "El apellido del ciudadano es obligatorio.",
     * )
     * @ORM\Column(name="apellido", type="string", length=80)
     */
    private $apellido;

    /**
     * Número de CUIL / CUIT del ciudadano
     *
     * @var integer
     * @Assert\NotNull(
     *     message="Este campo no puede estar vacío."
     * )
     * @ORM\Column(name="cuil", type="bigint", length=11)
     */
    private $cuil;

    /**
     * Email del ciudadano
     *
     * @var string
     * @Assert\NotNull(
     *     message="Este campo no puede estar vacío."
     * )
     * @Assert\Email(
     *     message = "Debe ingresar un email válido ej. juan@gmail.com.",
     *     checkMX = true
     * )
     * @ORM\Column(name="email", type="string", length=255)
     */
    private $email;

    /**
     * Teléfono del ciudadano
     *
     * @var string
     * @Assert\Type(
     *     type="string",
     *     message="Este campo debe contener solo números."
     * )
     * @ORM\Column(name="telefono", type="string", length=255, nullable=true)
     */
    private $telefono;

    /**
     * JSON conteniedo datos del ciudadano
     *
     * @var string
     * @Assert\NotNull(
     *     message="Este campo no puede estar vacío."
     * )
     * @Assert\Type(
     *     type="object",
     *     message="Este campo debe contener solo caracteres."
     * )
     * @ORM\Column(name="campos", type="json_array")
     */
    private $campos;

    /**
     * Fecha de creación
     *
     * @var \DateTime
     * @ORM\Column(name="fecha_creado", type="datetime")
     */
    private $fechaCreado;

    /**
     * Fecha de modificación
     *
     * @var \DateTime
     * @ORM\Column(name="fecha_modificado", type="datetime")
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
     * DatosTurno constructor.
     *
     * @param DatosTurno $datosTurno
     * @param TurnoHistorico $turno
     */
    public function __construct(DatosTurno $datosTurno, TurnoHistorico $turno)
    {
        $this->id = $datosTurno->getId();
        $this->nombre = $datosTurno->getNombre();
        $this->apellido = $datosTurno->getApellido();
        $this->cuil = $datosTurno->getCuil();
        $this->email = $datosTurno->getEmail();
        $this->telefono = $datosTurno->getTelefono();
        $this->turno = $turno;
        $this->campos = $datosTurno->getCampos();
        $this->fechaCreado = $datosTurno->getFechaCreado();
        $this->fechaModificado = $datosTurno->getFechaModificado();
        $this->fechaBorrado = $datosTurno->getFechaBorrado();
    }
}

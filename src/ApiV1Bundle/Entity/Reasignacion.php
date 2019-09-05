<?php

namespace ApiV1Bundle\Entity;

use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Validator\Constraints as Assert;
use Gedmo\Mapping\Annotation as Gedmo;

/**
 * Reasignacion
 *
 * @ORM\Table(name="reasignacion")
 * @ORM\Entity(repositoryClass="ApiV1Bundle\Repository\ReasignacionRepository")
 * @Gedmo\SoftDeleteable(fieldName="fechaBorrado")
 * @ORM\HasLifecycleCallbacks()
 */
class Reasignacion
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
     * JSON conteniedo datos de la reasignación
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
     * Campo para determinar si el mensaje de la reasignación ya fue enviado
     *
     * @var int
     * @Assert\NotNull(
     *     message="Este campo no puede estar vacío."
     * )
     * @Assert\Type(
     *     type="integer",
     *     message="Este campo no puede estar vacío y solo acepta caracteres numéricos."
     * )
     * @ORM\Column(name="enviada", type="smallint")
     */
    private $enviada;

    /**
     * ID del sistema de envío de notificaciones
     * @var int
     * @Assert\Type(
     *     type="integer",
     *     message="Este campo solo acepta caracteres numéricos."
     * )
     * @ORM\Column(name="notificacion_id", type="integer", nullable=true)
     */
    private $idNotificacion;

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

    public function __construct($campos)
    {
        $this->campos = $campos;
        $this->enviada = 0;
        $this->setFechas();
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
     * @return string
     */
    public function getCampos()
    {
        return $this->campos;
    }

    /**
     * @param string $campos
     */
    public function setCampos($campos)
    {
        $this->campos = $campos;
        return $this;
    }

    /**
     * @return int
     */
    public function getEnviada()
    {
        return $this->enviada;
    }

    /**
     * @param int $enviada
     */
    public function setEnviada($enviada)
    {
        $this->enviada = $enviada;
        return $this;
    }

    /**
     * @return int
     */
    public function getIdNotificacion()
    {
        return $this->idNotificacion;
    }

    /**
     * @param int $idNotificacion
     */
    public function setIdNotificacion($idNotificacion)
    {
        $this->idNotificacion = $idNotificacion;
        return $this;
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
        return $this;
    }

    /**
     * Actualiza la fecha modificación del grupo de trámite
     *
     * @ORM\PreUpdate
     */
    public function updatedFechas()
    {
        $this->fechaModificado = new \DateTime();
        return $this;
    }

    /**
     * @return \DateTime
     */
    public function getFechaCreado()
    {
        return $this->fechaCreado;
    }


    /**
     * @return \DateTime
     */
    public function getFechaModificado()
    {
        return $this->fechaModificado;
    }

    /**
     * @return \DateTime
     */
    public function getFechaBorrado()
    {
        return $this->fechaBorrado;
    }
}

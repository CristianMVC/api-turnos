<?php
namespace ApiV1Bundle\Entity;

use Doctrine\ORM\Mapping as ORM;
use ApiV1Bundle\Entity\User;

/**
 * Responsable
 *
 * @ORM\Table(name="user_punto_atencion")
 * @ORM\Entity(repositoryClass="ApiV1Bundle\Repository\UserPuntoAtencionRepository")
 * @ORM\HasLifecycleCallbacks()
 */
class UserPuntoAtencion extends Usuario
{
    /**
     * @var int
     *
     * @ORM\Column(name="id", type="integer")
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="AUTO")
     */
    protected $id;

    /**
     * Fecha de creación del usuario
     *
     * @var \DateTime
     * @ORM\Column(name="fecha_creado", type="datetimetz")
     */
    private $fechaCreado;

    /**
     * Fecha de modificación del usuario
     *
     * @var \DateTime
     * @ORM\Column(name="fecha_modificado", type="datetimetz")
     */
    private $fechaModificado;

    /**
     * Fecha de borrado del usuario
     *
     * @var \DateTime
     * @ORM\Column(name="fecha_borrado", type="datetimetz", nullable=true)
     */
    private $fechaBorrado;

    /**
     * Responsable constructor.
     * @param $nombre
     * @param $apellido
     * @param $puntoAtencion
     * @param User $user
     */
    public function __construct($nombre, $apellido, $organismo, $area, $puntoAtencion, User $user)
    {
        parent::__construct($nombre, $apellido, $user);
        $this->organismo = $organismo;
        $this->area = $area;
        $this->puntoAtencion = $puntoAtencion;
    }

    /**
     * Get id
     *
     * @return integer
     */
    public function getId()
    {
        return $this->id;
    }

    /**
     * Set fechaCreado
     *
     * @param \DateTime $fechaCreado
     *
     * @return UserPuntoAtencion
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
     * @return UserPuntoAtencion
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
     * @return UserPuntoAtencion
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
     * Genera las fechas de creación y modificación
     *
     * @ORM\PrePersist
     */
    public function setFechas()
    {
        $this->fechaCreado = new \DateTime();
        $this->fechaModificado = new \DateTime();
    }

    /**
     * Actualiza la fecha de modificación
     *
     * @ORM\PreUpdate
     */
    public function updatedFechas()
    {
        $this->fechaModificado = new \DateTime();
    }

    /**
     * Set puntoAtencion
     *
     * @param \ApiV1Bundle\Entity\PuntoAtencion $puntoAtencion
     *
     * @return UserPuntoAtencion
     */
    public function setPuntoAtencion(\ApiV1Bundle\Entity\PuntoAtencion $puntoAtencion = null)
    {
        $this->puntoAtencion = $puntoAtencion;

        return $this;
    }

    /**
     * Get puntoAtencion
     *
     * @return \ApiV1Bundle\Entity\PuntoAtencion
     */
    public function getPuntoAtencion()
    {
        return $this->puntoAtencion;
    }

    /**
     * Set user
     *
     * @param \ApiV1Bundle\Entity\User $user
     *
     * @return UserPuntoAtencion
     */
    public function setUser(\ApiV1Bundle\Entity\User $user = null)
    {
        $this->user = $user;

        return $this;
    }

    /**
     * Datos del organismo
     *
     * {@inheritDoc}
     * @see \ApiV1Bundle\Entity\Usuario::getOrganismoData()
     */
    public function getOrganismoData()
    {
        return [
            'id' => $this->puntoAtencion->getArea()->getOrganismo()->getId(),
            'nombre' => $this->puntoAtencion->getArea()->getOrganismo()->getNombre()
        ];
    }

    /**
     * Datos del area
     *
     * {@inheritDoc}
     * @see \ApiV1Bundle\Entity\Usuario::getAreaData()
     */
    public function getAreaData()
    {
        return [
            'id' => $this->puntoAtencion->getArea()->getId(),
            'nombre' => $this->puntoAtencion->getArea()->getNombre()
        ];
    }

    /**
     * Datos del punto de atencion
     *
     * {@inheritDoc}
     * @see \ApiV1Bundle\Entity\Usuario::getPuntoAtencionData()
     */
    public function getPuntoAtencionData()
    {
        return [
            'id' => $this->puntoAtencion->getId(),
            'nombre' => $this->puntoAtencion->getNombre()
        ];
    }

    public function getPuntoAtencionId()
    {
        return $this->getPuntoAtencion()->getId();
    }

    public function getAreaId()
    {
        return $this->getPuntoAtencion()->getArea()->getId();
    }

    public function getOrganismoId()
    {
        return $this->getPuntoAtencion()->getArea()->getOrganismo()->getId();
    }
}

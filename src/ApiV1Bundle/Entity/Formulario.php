<?php
namespace ApiV1Bundle\Entity;

use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Validator\Constraints as Assert;
use Gedmo\Mapping\Annotation as Gedmo;

/**
 * Class Formulario
 * @package ApiV1Bundle\Entity
 *
 * @ORM\Table(name="formulario")
 * @ORM\Entity(repositoryClass="ApiV1Bundle\Repository\FormularioRepository")
 * @Gedmo\SoftDeleteable(fieldName="fechaBorrado")
 * @ORM\HasLifecycleCallbacks()
 */

class Formulario
{

    /**
     * Formulario constructor.
     *
     * @param array $campos Arreglo con los datos de los campos del formulario
     */

    public function __construct($campos)
    {

        $this->setCampos($campos);
    }

    /**
     * Identificador del formulario, autoincremental
     *
     * @var int
     *
     * @ORM\Column(name="id", type="integer")
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="AUTO")
     */
    private $id;

    /**
     * Campo para relacionar el formulario con un único trámite
     * Un formulario pertenece a un solo tramite
     *
     * @var ApiV1Bundle/Entity/Tramite
     * @ORM\OneToOne(targetEntity="Tramite", mappedBy="formulario")
     */
    private $tramite;

    /**
     * JSON con los datos del formulario
     *
     * @var string
     * @Assert\Type(
     *     type="object"
     * )
     * @ORM\Column(name="campos", type="json_array")
     */
    private $campos;

    /**
     * Fecha de creación de formulario
     *
     * @var \DateTime
     * @ORM\Column(name="fecha_creado", type="datetimetz")
     */
    private $fechaCreado;

    /**
     * Fecha de modificación de formulario
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
     * Obtiene el identifidador único del formulario
     *
     * @return integer
     */
    public function getId()
    {
        return $this->id;
    }

    /**
     * Setea los campos del formulario
     *
     * @param array $campos
     * @return Formulario
     */
    public function setCampos($campos)
    {
        $this->campos = $campos;

        return $this;
    }

    /**
     * Obtiene los campos del formulario
     *
     * @return array
     */
    public function getCampos()
    {
        return $this->campos;
    }

    /**
     * Set fechaCreado - Fecha de creación del formulario
     *
     * @param \DateTime $fechaCreado
     * @return Formulario
     */
    public function setFechaCreado($fechaCreado)
    {
        $this->fechaCreado = $fechaCreado;

        return $this;
    }

    /**
     * Get fechaCreado - Obtiene la fecha de creación del formulario
     *
     * @return \DateTime
     */
    public function getFechaCreado()
    {
        return $this->fechaCreado;
    }

    /**
     * Setea la fecha de modificación del formulario
     *
     * @param \DateTime $fechaModificado
     * @return Formulario
     */
    public function setFechaModificado($fechaModificado)
    {
        $this->fechaModificado = $fechaModificado;

        return $this;
    }

    /**
     * Obtiene la fecha de modificación del formulario
     *
     * @return \DateTime
     */
    public function getFechaModificado()
    {
        return $this->fechaModificado;
    }

    /**
     * Setea un trámite para un formulario
     *
     * @param \ApiV1Bundle\Entity\Tramite $tramite
     * @return Formulario
     */
    public function setTramite(\ApiV1Bundle\Entity\Tramite $tramite = null)
    {
        $this->tramite = $tramite;

        return $this;
    }

    /**
     * Obtiene el trámite al que pertenece el formulario
     *
     * @return Tramite
     */
    public function getTramite()
    {
        return $this->tramite;
    }

    /**
     * Genera las fechas de creación y modificación Para un formulario
     *
     * @ORM\PrePersist
     */
    public function setFechas()
    {
        $this->fechaCreado = new \DateTime();
        $this->fechaModificado = new \DateTime();
    }

    /**
     * Actualiza la fecha de modificación del formulario
     *
     * @ORM\PreUpdate
     */
    public function updatedFechas()
    {
        $this->fechaModificado = new \DateTime();
    }
}

<?php

namespace ApiV1Bundle\Entity;

use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Validator\Constraints as Assert;

/**
 * PuntoTramite
 *
 * @ORM\Table(name="punto_tramite")
 * @ORM\Entity(repositoryClass="ApiV1Bundle\Repository\PuntoTramiteRepository")
 */
class PuntoTramite
{
    public function __construct($puntoAtencion, $tramite, $estado = 1, $multiple = 0, $multipleHorizonte = 0, $multipleMax = 0, $permiteOtro = 0, $permiteOtroCantidad = 0, $deshabilitar_hoy = 0, $permite_prioridad = 0, $multiturno = 0, $multiturno_cantidad = 0) {
        $this->puntoAtencion = $puntoAtencion;
        $this->tramite = $tramite;
        $this->estado = $estado;
        $this->multiple = $multiple;
        $this->multiple_horizonte = $multipleHorizonte;
        $this->multiple_max = $multipleMax;
        $this->permite_otro = $permiteOtro;
        $this->permite_otro_cantidad = $permiteOtroCantidad;
        $this->deshabilitar_hoy = $deshabilitar_hoy;
        $this->permite_prioridad = $permite_prioridad;
        $this->multiturno = $multiturno;
        $this->multiturno_cantidad = $multiturno_cantidad;
    }

    /**
     * @ORM\Column(name="id", type="integer")
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="AUTO")
     */
    private $id;

    /**
     * @var int
     * @ORM\ManyToOne(targetEntity="PuntoAtencion", inversedBy="tramites")
     * @ORM\JoinColumn(nullable=false, name="punto_atencion_id")
     */
    private $puntoAtencion;

    /**
     * @var int
     * @ORM\ManyToOne(targetEntity="Tramite", inversedBy="puntosAtencion")
     * @ORM\JoinColumn(nullable=false, name="tramite_id")
     */
    private $tramite;

    /**
     * @var int
     *
     * @Assert\NotNull(
     *     message="El campo Estado no puede estar vacío."
     * )
     * @Assert\Range(
     *     min=0,
     *     max=1
     * )
     * @ORM\Column(name="estado", type="smallint", options={"default": 1})
     */
    private $estado;

        /**
     * Acepta multiples turnos
     *
     * @var \integer
     * @ORM\Column(name="multiple", type="integer", options={"default": 0})
     */
    private $multiple;

    /**
     * Maximo numero de familiares por cuil 
     *
     * @var \integer
     * @ORM\Column(name="multiple_max", type="integer", options={"default": 0})
     */
    private $multiple_max;

    /**
     * horizonte a considerar para multiples cuil
     *
     * @var \integer
     * @ORM\Column(name="multiple_horizonte", type="integer", options={"default": 0})
     */
    private $multiple_horizonte;

       /**
     * Acepta sacar turnos para otra persona
     *
     * @var \integer
     * @ORM\Column(name="permite_otro", type="integer", options={"default": 0})
     */
    private $permite_otro;

    /**
     * Maximo numero de familiares por cuil 
     *
     * @var \integer
     * @ORM\Column(name="permite_otro_cantidad", type="integer", options={"default": 0})
     */
    private $permite_otro_cantidad;

    /**
     * Indica el estado del punto de atención
     *
     * @var int [0 => incativo, 1 => activo]
     * @Assert\NotNull(
     *     message="El campo Permitir prioridad no puede estar vacío."
     * )
     * @Assert\Range(
     *     min=0,
     *     max=1
     * )
     * @ORM\Column(name="permite_prioridad", type="smallint")
     */
    private $permite_prioridad;

    /**
     * Indica si está deshabilitado sacar el turno del día de hoy
     *
     * @var int [0 => incativo, 1 => activo]
     * @Assert\NotNull(
     *     message="El campo deshabilitar hoy no puede estar vacío."
     * )
     * @Assert\Range(
     *     min=0,
     *     max=1
     * )
     * @ORM\Column(name="deshabilitar_hoy", type="smallint")
     */
    private $deshabilitar_hoy;

    /**
     * punto atencion id
     *
     * @var \integer
     * @ORM\Column(name="punto_atencion_id", type="integer")
     */
    private $puntoAtencionId;
    /**
     * @return int
     */
    
     /**
     * Acepta sacar multiturnos para un grupo de personas
     *
     * @var \integer
     * @ORM\Column(name="multiturno", type="integer", options={"default": 0})
     */
    private $multiturno;

    /**
     * Maximo numero de familiares por turno 
     *
     * @var \integer
     * @ORM\Column(name="multiturno_cantidad", type="integer", options={"default": 0})
     */
    private $multiturno_cantidad;
    
    
    public function getPuntoAtencion()
    {
        return $this->puntoAtencion;
    }

    /**
     * @return int
     */
    public function getTramite()
    {
        return $this->tramite;
    }

    /**
     * @return int
     */
    public function getEstado()
    {
        return $this->estado;
    }

    /**
     * @param int $estado
     */
    public function setEstado($estado)
    {
        $this->estado = $estado;
    }

    /**
     * @return mixed
     */
    public function getId()
    {
        return $this->id;
    } /**
     * Get multiple
     *
     * @return integer
     */
    public function getMultiple()
    {
        return $this->multiple;
    }

    /**
     * Set multipleMax
     *
     * @param integer $multipleMax
     *
     * @return Tramite
     */
    public function setMultipleMax($multipleMax)
    {
        $this->multiple_max = $multipleMax;

        return $this;
    }
/**
     * Get multipleMax
     *
     * @return integer
     */
    public function getMultipleMax()
    {
        return $this->multiple_max;
    }


    /**
     * Set multipleHorizonte
     *
     * @param integer $multipleHorizonte
     *
     * @return Tramite
     */
    public function setMultipleHorizonte($multipleHorizonte)
    {
        $this->multiple_horizonte = $multipleHorizonte;

        return $this;
    }

    /**
     * Get multipleHorizonte
     *
     * @return integer
     */
    public function getMultipleHorizonte()
    {
        return $this->multiple_horizonte;
    }

    

    /**
     * Set multiple
     *
     * @param integer $multiple
     *
     * @return PuntoTramite
     */
    public function setMultiple($multiple)
    {
        $this->multiple = $multiple;

        return $this;
    }

    /**
     * Set permiteOtro
     *
     * @param integer $permiteOtro
     *
     * @return PuntoTramite
     */
    public function setPermiteOtro($permiteOtro)
    {
        $this->permite_otro = $permiteOtro;

        return $this;
    }

    /**
     * Get permiteOtro
     *
     * @return integer
     */
    public function getPermiteOtro()
    {
        return $this->permite_otro;
    }

    /**
     * Set permiteOtroCantidad
     *
     * @param integer $permiteOtroCantidad
     *
     * @return PuntoTramite
     */
    public function setPermiteOtroCantidad($permiteOtroCantidad)
    {
        $this->permite_otro_cantidad = $permiteOtroCantidad;

        return $this;
    }

    /**
     * Get permiteOtroCantidad
     *
     * @return integer
     */
    public function getPermiteOtroCantidad()
    {
        return $this->permite_otro_cantidad;
    }
   
    /**
     * Set permite_prioridad
     *
     * @param integer $permite_prioridad
     *
     * @return PuntoTramite
     */
    public function setPermitePrioridad($permite_prioridad)
    {
        $this->permite_prioridad = $permite_prioridad;

        return $this;
    }

    /**
     * Get permite_prioridad
     *
     * @return integer
     */
    public function getPermitePrioridad()
    {
        return $this->permite_prioridad;
    }

    /**
     * Set deshabilitar_hoy
     *
     * @param integer $deshabilitar_hoy
     *
     * @return PuntoTramite
     */
    public function setDeshabilitarHoy($deshabilitar_hoy)
    {
        $this->deshabilitar_hoy = $deshabilitar_hoy;

        return $this;
    }

    /**
     * Get deshabilitar_hoy
     *
     * @return integer
     */
    public function getDeshabilitarHoy()
    {
        return $this->deshabilitar_hoy;
    }

    /**
     * Set puntoAtencion
     *
     * @param \ApiV1Bundle\Entity\PuntoAtencion $puntoAtencion
     *
     * @return PuntoTramite
     */
    public function setPuntoAtencion(\ApiV1Bundle\Entity\PuntoAtencion $puntoAtencion)
    {
        $this->puntoAtencion = $puntoAtencion;

        return $this;
    }

    /**
     * Set tramite
     *
     * @param \ApiV1Bundle\Entity\Tramite $tramite
     *
     * @return PuntoTramite
     */
    public function setTramite(\ApiV1Bundle\Entity\Tramite $tramite)
    {
        $this->tramite = $tramite;

        return $this;
    }
    
    /**
     * @return int
     */
    public function getPuntoAtencionId()
    {
        return $this->puntoAtencionId;
    }
    
    
     /**
     * Set multiturno
     *
     * @param integer $multiturno
     *
     * @return PuntoTramite
     */
    public function setMultiturno($multiturno)
    {
        $this->multiturno = $multiturno;

        return $this;
    }

    /**
     * Get multiturno
     *
     * @return integer
     */
    public function getMultiturno()
    {
        return $this->multiturno;
    }

    /**
     * Set multiturno_cantidad
     *
     * @param integer $multiturnoCantidad
     *
     * @return PuntoTramite
     */
    public function setMultiturnoCantidad($multiturnoCantidad)
    {
        $this->multiturno_cantidad = $multiturnoCantidad;

        return $this;
    }

    /**
     * Get multiturno_cantidad
     *
     * @return integer
     */
    public function getMultiturnoCantidad()
    {
        return $this->multiturno_cantidad;
    }
}

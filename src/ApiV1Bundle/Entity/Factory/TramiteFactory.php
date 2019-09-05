<?php
namespace ApiV1Bundle\Entity\Factory;

use ApiV1Bundle\Entity\Tramite;
use ApiV1Bundle\Entity\ValidateResultado;
use ApiV1Bundle\Helper\FormHelper;
use ApiV1Bundle\Repository\AreaRepository;
use ApiV1Bundle\Entity\Validator\TramiteValidator;

/**
 * Class TramiteFactory
 * @package ApiV1Bundle\Entity\Factory
 */

class TramiteFactory
{
    /** @var AreaRepository  */
    private $areaRepository;
    /** @var FormularioFactory  */
    private $formularioFactory;
    /** @var TramiteValidator  */
    private $tramiteValidator;

    /**
     * TramiteFactory constructor.
     * @param AreaRepository $areaRepository
     * @param FormularioFactory $formularioFactory
     * @param TramiteValidator $tramiteValidator
     */
    public function __construct(
        AreaRepository $areaRepository,
        FormularioFactory $formularioFactory,
        TramiteValidator $tramiteValidator
    ) {
        $this->areaRepository = $areaRepository;
        $this->formularioFactory = $formularioFactory;
        $this->tramiteValidator = $tramiteValidator;
    }

    /**
     * Creación de un Tramite. El Tramite Factory valida los datos que vienen por params y crea un nuevo tramite.
     *
     * @param array $params array con los datos del trámite a crear
     * @return mixed
     */
    public function create($params)
    {

        $validateResultado = $this->tramiteValidator->validarCreate($params);

        if (!$validateResultado->hasError()) {

            $areas =  $this->tramiteValidator->getArea();

            $tramite = new Tramite($params['nombre'], (int) $params['visibilidad']);


            $tramite->addArea($areas);


            $tramite->setDuracion((int) $params['duracion']);
            $tramite->setDescripcion( $params['descripcion']);            
           $tramite->setMiargentina($params['miArgentina']);
            $tramite->setOrg($params['org']);

            $tramite->setRequisitos($params['requisitos']);
            $tramite->setExcepcional($params['excepcional']);
            if (isset($params['campos'])) {
                $validateResultado = $this->formularioFactory->create(FormHelper::datosFormulario($params['campos']));
                if (!$validateResultado->hasError()) {
                    $formulario = $validateResultado->getEntity();
                    $tramite->setFormulario($formulario);
                }else{
                    return $validateResultado;
                }
            }
            if (isset($params['idArgentinaGobAr'])) {
                $tramite->setIdArgentinaGobAr($params['idArgentinaGobAr']);
            }

            return new ValidateResultado($tramite, []);
        }
        return $validateResultado;
    }
}
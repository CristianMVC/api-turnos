<?php

namespace ApiV1Bundle\Entity\Sync;

use ApiV1Bundle\Entity\Tramite;
use ApiV1Bundle\Repository\AreaRepository;
use ApiV1Bundle\Repository\TramiteRepository;
use ApiV1Bundle\Entity\ValidateResultado;
use ApiV1Bundle\Entity\Factory\FormularioFactory;
use ApiV1Bundle\Entity\Sync\FormularioSync;
use ApiV1Bundle\Helper\FormHelper;
use ApiV1Bundle\Entity\Validator\TramiteValidator;
use ApiV1Bundle\Repository\TurnoRepository;

/**
 * Class TramiteSync
 * @package ApiV1Bundle\Entity\Sync
 *
 *  @author jtibi
 *
 */

class TramiteSync
{
    /** @var AreaRepository  */
    private $areaRepository;
    /** @var TramiteRepository  */
    private $tramiteRepository;
    /** @var TramiteValidator  */
    private $tramiteValidator;
    /** @var FormularioFactory  */
    private $formularioFactory;
    /** @var \ApiV1Bundle\Entity\Sync\FormularioSync  */
    private $formularioSync;
    /** @var TurnoRepository  */
    private $turnoRepository;

    /**
     * TramiteSync constructor.
     * @param AreaRepository $areaRepository
     * @param TramiteRepository $tramiteRepository
     * @param FormularioFactory $formularioFactory
     * @param TramiteValidator $tramiteValidator
     * @param \ApiV1Bundle\Entity\Sync\FormularioSync $formularioSync
     * @param TurnoRepository $turnoRepository
     */
    public function __construct(
        AreaRepository $areaRepository,
        TramiteRepository $tramiteRepository,
        FormularioFactory $formularioFactory,
        TramiteValidator $tramiteValidator,
        FormularioSync $formularioSync,
        TurnoRepository $turnoRepository
    ) {
        $this->areaRepository = $areaRepository;
        $this->tramiteRepository = $tramiteRepository;
        $this->formularioFactory = $formularioFactory;
        $this->tramiteValidator = $tramiteValidator;
        $this->formularioSync = $formularioSync;
        $this->turnoRepository = $turnoRepository;
    }

    /**
     * Modificación de un Tramite. El Tramite Sync valida los datos que vienen por params y modifica un nuevo tramite.
     *
     * @param integer $id Identificador único del trámite
     * @param array $params arreglo con datos para la edición del trámite
     * @return mixed
     */
    public function edit($id, $params)
    {
        $validateResultados = $this->tramiteValidator->validarEdit($params, $id);
        $areas = null;

        if (!$validateResultados->hasError()) {
            $tramite = $this->tramiteRepository->find($id);

            foreach ($params['area'] as $p) {

                $areas[] =  $this->areaRepository->find($p);
            }


            $formulario = $tramite->getFormulario();
            if (!$formulario) {
                $validateResultado = $this->formularioFactory->create(FormHelper::datosFormulario($params['campos']));
                if (!$validateResultado->hasError()) {
                    $formulario = $validateResultado->getEntity();
                }else{
                    return $validateResultado;
                }
            } else {
                $validateResultado = $this->formularioSync->edit($formulario,$params['campos']);
                if (!$validateResultado->hasError()) {
                    $formulario = $validateResultado->getEntity();
                }else{
                    return $validateResultado;
                }
            }

            $tramite->setFormulario($formulario);
            $tramite->removeArea($tramite->getArea());
            $tramite->addArea($areas);
            $tramite->setNombre($params['nombre']);
            $tramite->setDescripcion($params['descripcion']);
            $tramite->setVisibilidad((int)$params['visibilidad']);
            $tramite->setDuracion((int)$params['duracion']);
            $tramite->setRequisitos($params['requisitos']);
            $tramite->setExcepcional($params['excepcional']);
            $tramite->setMiargentina($params['miArgentina']);
            if (isset($params['idArgentinaGobAr'])) {
                $tramite->setIdArgentinaGobAr($params['idArgentinaGobAr']);
            }
            return new ValidateResultado($tramite, []);
        }
        return $validateResultados;
    }

    /**
     * Borrar un trámite
     *
     * @param integer $id Identificador único del trámite
     * @return mixed
     */
    public function delete($id, $idArea)
    {
        $tramite = $this->tramiteRepository->find($id);

       if($idArea and count($tramite->getArea()) > 1) {
           $area[] = $this->areaRepository->find($idArea);
           $tramite->removeArea($area);

       } else {

           $tramite->removeArea($tramite->getArea());
       }

        $validateResultado = $this->tramiteValidator->validarDelete($tramite, $this->turnoRepository);
        if (! $validateResultado->hasError()) {
            //Se borra la relación uno a uno para que borre el registro fícsico en tramite_grupotramite
            foreach ($tramite->getGrupoTramites() as $grupoTramite){
                $grupoTramite->removeTramite($tramite);
            }
            $validateResultado->setEntity($tramite);
        }

        return $validateResultado;
    }

}

<?php

namespace ApiV1Bundle\ApplicationServices;
use ApiV1Bundle\Entity\Factory\DiaNoLaborableTramiteFactory;
use ApiV1Bundle\Repository\DiaNoLaborableTramiteRepository;
use ApiV1Bundle\Entity\Validator\DiaNoLaborableTramiteValidator;
use Symfony\Component\DependencyInjection\Container;
use ApiV1Bundle\Repository\PuntoAtencionRepository;
use ApiV1Bundle\Repository\TramiteRepository;
use ApiV1Bundle\Entity\ValidateResultado;
use ApiV1Bundle\Entity\DiaNoLaborableTramite;

/**
 * Class DiaNoLaborableTramiteServices
 * @package ApiV1Bundle\ApplicationServices
 */
class DiaNoLaborableTramiteServices extends SNTServices
{

    /** @var DiaNoLaborableTramiteRepository  */
    private $diaNoLaborableTramiteRepository;
    /* @var DiaNoLaborableTramiteValidator*/
    private $diaNoLaborableValidator;
    /* @var PuntoAtencionRepository*/
    private $puntoAtencionRepository;
    /* @var tramiteRepository*/
    private $tramiteRepository;

    /**
     * DiaNoLaborableTramiteServices constructor.
     * @param Container $container
     * @param DiaNoLaborableTramiteRepository $diaNoLaborableTramiteRepository
     */
    public function __construct(
        Container $container,
        DiaNoLaborableTramiteRepository $diaNoLaborableTramiteRepository,
        DiaNoLaborableTramiteValidator $diaNoLaborableValidator,
        PuntoAtencionRepository $puntoAtencionRepository,
        TramiteRepository $tramiteRepository
    ) {
        parent::__construct($container);
        $this->diaNoLaborableTramiteRepository = $diaNoLaborableTramiteRepository;
        $this->diaNoLaborableValidator = $diaNoLaborableValidator;
        $this->puntoAtencionRepository = $puntoAtencionRepository;
        $this->tramiteRepository = $tramiteRepository;
    }

     /**
     * Crear nuevo 
     *
     * @param array $params arreglo con los datos del usuario
     * @param callback $onSuccess Callback para devolver respuesta exitosa
     * @param callback $onError Callback para devolver respuesta fallida
     * @return mixed
     */
    public function create($params, $punto_atencion_id, $tramite_id, $success, $error)
    {
        $DiaNoLaborableTramiteFactory = new DiaNoLaborableTramiteFactory($this->diaNoLaborableValidator,
                $this->puntoAtencionRepository,
                $this->tramiteRepository);
        $validateResult = $DiaNoLaborableTramiteFactory->create($params, $punto_atencion_id, $tramite_id);
        return $this->processResult(
            $validateResult,
            function ($entity) use ($success) {
                return call_user_func($success,  $this->diaNoLaborableTramiteRepository->save($entity));
            },
            $error
        );
    }
    
    
    /**
     * Listado de todos los dias no laborables
     *
     * @param integer $id Identificador único del punto de atención
     * @param callback $onError Callback para devolver respuesta fallida
     * @return mixed
     */
    public function getDiasNoLaborables($punto_atencion_id, $tramite_id, $onError)
    {
        $result = [];
        $errors = [];
        $tramite = $this->tramiteRepository->find($tramite_id);
        $puntoAtencion = $this->puntoAtencionRepository->find($punto_atencion_id);
        
        $diasNoLaborables = $this->diaNoLaborableTramiteRepository->findAllByTramitePda( $puntoAtencion,$tramite);
        
        $validateResultado = new ValidateResultado(null, $errors);
        if (! $validateResultado->hasError()) {
         //   $diaNoLaborable = $puntoAtencion->getDiasNoLaborables();

            foreach ($diasNoLaborables as $diaNoLaboral) {
                $result[] = $diaNoLaboral->getFecha()->format('Y-m-d');
            }
        }

        return $this->processError(
            $validateResultado,
            function () use ($result) {
                return $this->respuestaData([], $result);
            },
            $onError
        );
    }
    
    /**
     * Habilitar una fecha como día habil
     *
     * @param integer $id Identificador único para un punto de atención
     * @param array $params arreglo con la fecha
     * @return mixed
     */
    public function habilitarFechaTramite($params, $punto_atencion_id, $tramite_id)
    {
        
        $errors = [];
        $validateResultado = new ValidateResultado(null, $errors);
        $tramite = $this->tramiteRepository->find($tramite_id);
        $puntoAtencion = $this->puntoAtencionRepository->find($punto_atencion_id);
        
        $fecha = new \DateTime($params['fecha']);

        //get dia no laborable
        $diaNoLaborable = $this->diaNoLaborableTramiteRepository->findOneBy([
            'fecha' => $fecha,
            'tramite' => $tramite,
            'puntoAtencion' => $puntoAtencion
        ]);

        if ($diaNoLaborable) {
            //remove dia no laborable al punto de atencion
            $this->diaNoLaborableTramiteRepository->remove($diaNoLaborable);
        }
        return new ValidateResultado($puntoAtencion, []);
    }
    
    /**
     * Inhabilitar día
     *
     * @param object $puntoAtencion PuntoAtencion
     * @param array $params arreglo con la fecha
     * @return ValidateResultado
     */
    public function inhabilitarDia($punto_atencion_id, $tramite_id,  $params)
    {
        $errors = [];
        $validateResultado = new ValidateResultado(null, $errors);
        $tramite = $this->tramiteRepository->find($tramite_id);
        $puntoAtencion = $this->puntoAtencionRepository->find($punto_atencion_id);
        
        $fecha = new \DateTime($params['fecha']);

        if (!$validateResultado->hasError()) {
            $fecha = new \DateTime($params['fecha']);
            $diaNoLaborable = new DiaNoLaborableTramite($fecha, $puntoAtencion,$tramite);
            $this->diaNoLaborableTramiteRepository->save($diaNoLaborable);
            $validateResultado = new ValidateResultado($diaNoLaborable, []);
        }

        return $validateResultado;
    }
}

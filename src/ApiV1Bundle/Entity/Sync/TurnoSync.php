<?php
namespace ApiV1Bundle\Entity\Sync;

use ApiV1Bundle\Entity\Turno;
use ApiV1Bundle\Entity\ValidateResultado;
use ApiV1Bundle\Repository\TurnoRepository;
use ApiV1Bundle\Repository\PuntoAtencionRepository;
use ApiV1Bundle\Entity\Factory\DatosTurnoFactory;
use ApiV1Bundle\Entity\Validator\TurnoValidator;
use ApiV1Bundle\Helper\ServicesHelper;
use ApiV1Bundle\ExternalServices\NotificationsExternalService;

/**
 * Class TurnoSync
 * @package ApiV1Bundle\Entity\Sync
 */

class TurnoSync
{
    /** @var TurnoRepository  */
    private $turnoRepository;
    /** @var DatosTurnoFactory  */
    private $datosTurnoFactory;
    /** @var TurnoValidator  */
    private $turnoValidator;
    /** @var NotificationsExternalService  */
    private $notificationServices;
    /** @var PuntoAtencionRepository  */
    private $puntoAtencionRepository;

    /**
     * TurnoSync constructor.
     * @param TurnoRepository $turnoRepository
     * @param DatosTurnoFactory $datosTurnoFactory
     * @param TurnoValidator $turnoValidator
     * @param NotificationsExternalService $notificationServices
     */
    public function __construct(
        TurnoRepository $turnoRepository,
        DatosTurnoFactory $datosTurnoFactory,
        TurnoValidator $turnoValidator,
        NotificationsExternalService $notificationServices
    ) {
        $this->turnoRepository = $turnoRepository;
        $this->datosTurnoFactory = $datosTurnoFactory;
        $this->turnoValidator = $turnoValidator;
        $this->notificationServices = $notificationServices;
    }

    /**
     * Modificación de un turno. Sync valida los datos que vienen dentro de params para modificar un turno
     *
     * @param integer $id Identificador único del turno
     * @param array $params array con los datos del turno
     * @return mixed
     */
    public function edit($id, $params)
    {
      
        $turno = $this->turnoRepository->find($id);
        $validaTurno = $this->turnoValidator->validaTurnoConfirma($turno, $params);

        if (! $validaTurno->hasError()) {
            $campos = ServicesHelper::toArray($params['campos']);
            $datosTurno = $turno->getDatosTurno();

            if (! $datosTurno) {
                $campos['telefono'] = isset($campos['telefono']) ? $campos['telefono'] : null;

                // creamos los datos del turno
                $datosTurno = $this->datosTurnoFactory->create(
                    $campos['nombre'],
                    $campos['apellido'],
                    ServicesHelper::buildValidDocument($campos['cuil']),
                    $campos['email'],
                    $campos['telefono'],
                    $params['campos']
                );

                $turno->setDatosTurno($datosTurno);
            }
            // actualizamos el turno
            if(isset($params['cuilSolicitante'])){
                $turno->setCuil($params['cuilSolicitante']);
            }    
            $turno->setEstado(1);
            $turno->setAlerta($params['alerta']);
            return new ValidateResultado($turno, []);
        }
        $errors = $validaTurno->getErrors();
        return new ValidateResultado(null, $errors['errors']);
    }

    /**
     * Eliminar turno
     *
     * @param integer $id Identificador único del turno
     * @return mixed
     */
    public function delete($id)
    {
        $turno = $this->turnoRepository->find($id);
        if (! $turno) {
            return new ValidateResultado(null, ['Turno inexistente']);
        }
        return new ValidateResultado($turno, []);
    }

    /**
     * Cancelar turno
     *
     * @param string $codigo código del turno
     * @return mixed
     */
    public function cancel($codigo)
    {
        $turno = $this->turnoRepository->findOneByCodigo($codigo);
        if (! $turno) {
            return new ValidateResultado(null, ['Turno inexistente']);
        }
        // actualizamos el turno
        $turno->setEstado(Turno::ESTADO_CANCELADO);
        return new ValidateResultado($turno, []);
    }

    /**
     * Cancelar turnos por punto de atención y fecha
     *
     * @param array $params arreglo con los datos para calnelar el turno (fecha, punto de atención y motivo)
     * @return mixed
     */
    public function cancelByDate($params)
    {
        $validateResult = $this->turnoValidator->validarCancelByDate($params);
        if (! $validateResult->hasError()) {
            $fecha = new \DateTime($params['fecha']);
            $puntoAtencionId = $params['puntoatencion'];
            $turnos = $this->turnoRepository->getTurnosByFecha($fecha, $puntoAtencionId);
            foreach ($turnos as $turno) {
                $turnoCancelado = $this->turnoRepository->find($turno['id']);
                $turnoCancelado->setEstado(Turno::ESTADO_CANCELADO);
            }
            return new ValidateResultado($turnos, []);
        }
        $errors = $validateResult->getErrors();
        return new ValidateResultado(null, $errors['errors']);
    }

    /**
     * Modificación de fecha y hora de un turno. Sync valida los datos que vienen dentro de params para modificar fecha y hora un turno
     *
     * @param integer $id Identificador único del turno
     * @param array $params array con los datos del turno
     * @return mixed
     */
    public function modificar($params, PuntoAtencionRepository $puntoAtencionRepository)
    {
        $validaFechaYHora = $this->turnoValidator->validateFechaYHora($params);

        if ($validaFechaYHora->hasError()){
            $errors['errors'] = $validaFechaYHora->getErrors()['errors'];
            return new ValidateResultado(null, $errors['errors']);
        }

        $turno = $this->turnoRepository->searchOneByCodigo($params['codigo']);

        if ($turno) {

            // actualizamos el turno
            if (isset($params['punto_atencion_id'])){
                $puntoatencion = $puntoAtencionRepository->find($params['punto_atencion_id']);
                if (!$puntoatencion){
                    $errors['errors'] = "No existe el punto de atención.";
                    return new ValidateResultado(null, $errors['errors']);
                }
                $turno->setPuntoAtencion($puntoatencion);
            }

            if (isset($params['fecha'])){
                $fecha = new \DateTime($params['fecha']);
                $turno->setFecha($fecha);
            }

            if (isset($params['hora'])){
                $hora = new \DateTime($params['hora']);
                $turno->setHora($hora);
            }

            return new ValidateResultado($turno, []);
        }
        $errors['errors'] = "Turno no existente";

        return new ValidateResultado(null, $errors['errors']);
    }

    /**
     * Modificación de Multiple  turno. Sync valida los datos que vienen dentro de params para modificar un turno
     *
     * @param array $params array con los datos del turno
     * @return mixed
     */
    public function editMultiple( $params){
        
        $validaTurno = $this->turnoValidator->validaTurnoConfirmaMultiple( $params);

        if (! $validaTurno->hasError()) {
            foreach ($params["turnos"] as $arrTurno) {
                $id = $arrTurno["id"];
                $validateResultado = $this->edit($id, $arrTurno);
                
                if ($validateResultado->hasError()) {
                    $errors = $validateResultado->getErrors();
                    return new ValidateResultado(null, $errors['errors']);
                }
                $turnos[]=$validateResultado;
            }
            return new ValidateResultado($turnos, []);
           
        }
        $errors = $validaTurno->getErrors();
        return new ValidateResultado(null, $errors['errors']);
    }
}

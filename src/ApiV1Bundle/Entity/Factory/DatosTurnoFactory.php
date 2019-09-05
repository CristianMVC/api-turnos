<?php
namespace ApiV1Bundle\Entity\Factory;

use ApiV1Bundle\Entity\DatosTurno;
use ApiV1Bundle\Helper\FormHelper;

/**
 * Class DatosTurnoFactory
 * @package ApiV1Bundle\Entity\Factory
 */

class DatosTurnoFactory
{

    /**
     * Crea un objeto datosTurno
     *
     * @param string $nombre nombre del ciudadano
     * @param string $apellido apellido del ciudadano
     * @param integer $cuil Nro de CUIL - CUIT del ciudadano
     * @param string $email email del ciudadano
     * @param string $telefono teléfono del ciudadano
     * @param object $campos objeto JSON
     * @return mixed
     */

    public function create($nombre, $apellido, $cuil, $email, $telefono, $campos)
    {
        $datosTurno = new DatosTurno($nombre, $apellido, $cuil, $email, $telefono);
        $datosTurno->setCampos(FormHelper::datosFormulario($campos));
        return $datosTurno;
    }
}

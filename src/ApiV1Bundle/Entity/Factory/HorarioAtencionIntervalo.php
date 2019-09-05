<?php

namespace ApiV1Bundle\Entity\Factory;

/**
 * Class HorarioAtencionIntervalo
 * @package ApiV1Bundle\Entity\Factory
 */
class HorarioAtencionIntervalo
{

    /**
     * Obtiene el máximo intervalo disponible para utilizar en un Grupo de Tramite entre el que se envía por parametro
     * y los persistidos en la base de datos
     *
     * @param array $params arreglo con las horas del intérvalo
     * @param array $horariosAtencion arreglo con los Horarios de atención
     * @return int
     */
    public function getIntervalo($params, $horariosAtencion){
        $intervalo = $this->getIntervaloMaximo($horariosAtencion);

        $horaInicio = new \DateTime( $params['horaInicio']);
        $horaFin = new \DateTime($params['horaFin']);

        $diff = date_diff($horaFin, $horaInicio);
        //en caso que la dif sea 0, se transforma a máixmo de 60min
        $diff->i = ($diff->i == 0) ? 60 : $diff->i;
        //en caso que la dif sea 45min, se transforma a máximo de 15min
        $diff->i = ($diff->i == 45) ? 15 : $diff->i;

        if ($diff->i < $intervalo) {
            $intervalo = $diff->i;
        }

        return (int) $intervalo;
    }

    /**
     * Devuelve el máximo exceso de minutos con respecto a horas completas de
     * una colección de rangos horarios.
     *
     * @param array $horariosAtencion arreglo con los Horarios de atención
     * @return int
     */
    public function getIntervaloMaximo($horariosAtencion)
    {
        $intervalo = 60;
        foreach ($horariosAtencion as $horario) {
            /** @var HorarioAtencion $horario */
            $diff = date_diff($horario->getHoraInicio(), $horario->getHoraFin());
            $diff->i = ($diff->i == 0) ? 60 : $diff->i;
            $diff->i = ($diff->i == 45) ? 15 : $diff->i;
            if ($diff->i < $intervalo) {
                $intervalo = $diff->i;
            }
        }
        return $intervalo;
    }

}
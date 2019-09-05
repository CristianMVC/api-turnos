<?php
namespace ApiV1Bundle\Helper;
/**
 * Class ServicesHelper
 * @package ApiV1Bundle\Helper
 */
class ServicesHelper
{
    /**
     * Convertir en array
     *
     * @param mixed $data
     * @return mixed
     */
    public static function toArray($data)
    {
        if (is_array($data)) {
            return $data;
        }
        if (json_decode($data)) {
            return json_decode($data, true);
        }
        return null;
    }

    /**
     * Check if is array
     *
     * @param mixed $data
     * @return boolean
     */
    public static function isArray($data)
    {
        if (is_array($data)) {
            return true;
        }
        if (json_decode($data)) {
            return true;
        }
        return false;
    }

    /**
     * Retorna el horizonte en fracción
     *
     * @param integer $intervalo
     * @return float|int
     */
    public static function fraccionHoraria($intervalo)
    {
        return $intervalo;
        //return $intervalo / 60;
    }

    /**
     * Retorna el intervalo como entero
     *
     * @param integer $intervalo
     *
     * @return int
     */
    public static function transformaFracionHoraria($intervalo)
    {
        return $intervalo;
        //return $intervalo * 60;
    }

    /**
     * Retorna el listado de intervalos
     * @Todo modificar la función para que devuelva los intervalos desde una clase de intervalos
     *
     * @return array
     */
    public static function intervalos()
    {
        return [10, 15, 30, 60];
    }

    /**
     * Retorna el cuil sin '-'
     * @param string $cuil CUIL del ciudadano
     * @return integer
     */
    public static function buildValidCuil($cuil)
    {
        $cuil = str_replace('-', '', $cuil);
        return $cuil;
    }

    /**
     * Retorna el documento sin guiones
     *
     * @param string $document documento del ciudadano
     * @return string
     */
    public static function buildValidDocument($document)
    {
        return str_replace('-', '', $document);
    }

    /**
     * Retorna listado de horarios
     *
     * @return array
     */
    public static function listadoHorario()
    {
        return [
            '00:00',
            '01:00',
            '02:00',
            '03:00',
            '04:00',
            '05:00',
            '06:00',
            '07:00',
            '08:00',
            '09:00',
            '10:00',
            '11:00',
            '12:00',
            '13:00',
            '14:00',
            '15:00',
            '16:00',
            '17:00',
            '18:00',
            '19:00',
            '20:00',
            '21:00',
            '22:00',
            '23:00'
        ];
    }

    /**
     * Pasa un código unico a sus primeros 8 caracters
     *
     * @param string $code
     * @return string
     */
    public static function obtenerCodigoSimple($code)
    {
        $parts = explode('-', $code);
        return $parts[0];
    }

    /**
     * Transformar los requisitos de array a lista
     *
     * @param array $requisitos
     * @return string
     */
    public static function getRequisitosTramite($requisitos)
    {
        $requisitos = explode('|', $requisitos);
        $requisitosStr = '<ul>';
        foreach ($requisitos as $requisito) {
            $requisitosStr .= '<li>' . $requisito . '</li>';
        }
        $requisitosStr .= '</ul>';
        return $requisitosStr;
    }

    /**
     * Generar contraseña al azar
     *
     * @param integer $len longitud
     * @return string
     */
    public static function randomPassword($len = 8)
    {
        $chars = 'abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ1234567890';
        $pass = [];
        $alphaLength = strlen($chars) - 1;
        for ($i = 0; $i < $len; $i ++) {
            $n = rand(0, $alphaLength);
            $pass[] = $chars[$n];
        }
        return implode($pass);
    }

    /**
     * Validar formato de CUIL
     *
     * @param $campos
     * @param $key
     * @return string|NULL
     */
    public static function cuil($campos, $key)
    {
        $cuil = str_replace('-', '', $campos[$key]);
        $digitos = str_split($cuil);
        $digitoVerificador = array_pop($digitos);
        $cuilLen = strlen($cuil);
        if ($cuilLen === 10 || $cuilLen === 11) {
            $acumulado = 0;
            $diff = ($cuilLen == 11) ? 9 : 8;
            for ($i = 0; $i < count($digitos); $i++) {
                $acumulado += $digitos[$diff - $i] * (2 + ($i % 6));
            }
            $verif = 11 - ($acumulado % 11);
            $verificacion = ($verif == 11) ? 0 : $verif;
            if ($digitoVerificador == $verificacion) {
                return true;
            }
        }
        return false;
    }

    /**
     * Convertir requisitos de string a array
     *
     * @param string $requisitos
     * @return array
     */
    public static function parseRequisitos($requisitos)
    {
        if ($requisitos) {
            return explode(PHP_EOL, $requisitos);
        }
        return [];
    }

    /**
     * Convertir requisitos de array a string
     *
     * @param array $requisitos
     * @return string
     */
    public static function mergeRequisitos($requisitos)
    {
        if ($requisitos) {
            return implode(PHP_EOL, ServicesHelper::toArray($requisitos));
        }
        return '';
    }
}

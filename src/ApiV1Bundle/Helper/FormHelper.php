<?php
namespace ApiV1Bundle\Helper;

/**
 * Class FormHelper
 * @package ApiV1Bundle\Helper
 */
class FormHelper
{

    /**
     * Helper de Datos del formulario
     * Si recibe un array lo retorna, si recibe un json lo decodifica y lo retorna, sino por defecto retorna un array vacío.
     *
     * @param $data
     * @return array|mixed
     */
    public static function datosFormulario($data)
    {
        if (is_array($data)) {
            return $data;
        }
        if (json_decode($data)) {
            return json_decode($data, true);
        }
        return [];
    }

    /**
     * Valida y convierte un string en array
     *
     * @param mixed $data
     * @return Array|NULL
     */
    public static function camposFormulario($data)
    {
        if (is_array($data)) {
            return $data;
        }
        if (is_string($data)) {
            return json_decode($data, true);
        }
        return null;
    }
}

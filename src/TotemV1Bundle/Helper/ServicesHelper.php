<?php
namespace TotemV1Bundle\Helper;

/**
 * Class ServicesHelper
 * @package TotemV1Bundle\Helper
 */
class ServicesHelper
{
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
     * Clean CUIL
     * @param string $cuil
     * @return mixed
     */
    public static function cleanCUIL($cuil)
    {
        return preg_replace('/\D/', '', $cuil);
    }
}

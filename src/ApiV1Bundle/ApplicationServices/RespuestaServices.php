<?php
namespace ApiV1Bundle\ApplicationServices;

/**
 * Class RespuestaServices
 * @package ApiV1Bundle\ApplicationServices
 */

class RespuestaServices
{
    /** @var array $metadata */
    private $metadata;
    /** @var array $result */
    private $result;

    /**
     * RespuestaServices constructor.
     *
     * @param $metadata
     * @param $result
     */

    public function __construct($metadata, $result)
    {
        $this->metadata = $metadata;
        $this->result = $result;
    }

    /**
     * Obtiene metadata de la respuesta de los servicios
     *
     * @return mixed
     */
    public function getMetadata()
    {
        return $this->metadata;
    }

    /**
     * Obtiene un resultado solicitado
     *
     * @return mixed
     */
    public function getResult()
    {
        return $this->result;
    }
}

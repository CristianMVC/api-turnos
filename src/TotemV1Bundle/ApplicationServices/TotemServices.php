<?php

namespace TotemV1Bundle\ApplicationServices;

use TotemV1Bundle\Entity\Response\Respuesta;
use Symfony\Component\DependencyInjection\Container;
use TotemV1Bundle\Entity\ValidateResultado;

/**
 * Class TotemServices
 * @package TotemV1Bundle\ApplicationServices
 */

class TotemServices
{
    /** @var Container  */
    private $container;

    /**
     * SNTServices constructor.
     *
     * @param Container $container
     */
    public function __construct(Container $container)
    {
        $this->container = $container;
    }

    /**
     * Obtenemos uno de los parametros de la configuración
     *
     * @param array $parameter
     * @return mixed
     */
    public function getParameter($parameter)
    {
        return $this->container->getParameter($parameter);
    }

    /**
     * Obtener security Password
     *
     * @return mixed
     */
    protected function getSecurityPassword()
    {
        return $this->container->get('security.password_encoder');
    }

    /**
     * Valida una entidad que recibe por parámetro
     *
     * @param object $entity Objeto entidad
     * @return mixed
     */
    protected function validate($entity)
    {
        $response = [
            'errors' => []
        ];
        $errors = $this->container->get('validator')->validate($entity);

        if (count($errors)) {
            foreach ($errors as $error) {
                $response['errors'][$error->getPropertyPath()] = $error->getMessage();
            }
        }
        return $response;
    }

    /**
     * Retorna la cantidad de errores que se produjeron
     *
     * @param array $errors Array con los errores que se produjeron
     * @return int
     */
    protected function hasErrors($errors)
    {
        return (count($errors['errors']));
    }

    /**
     * Valida el resutlado del proceso
     *
     * @param object $validateResult Objeto a validar
     * @param callback $sucess Callback para devolver respuesta exitosa
     * @param callback $error Callback para devolver respuesta fallida
     * @return mixed
     */
    protected function processResult($validateResult, $onSucess, $onError)
    {
        if ($validateResult->hasError()) {
            return call_user_func($onError, $validateResult->getErrors());
        } else {
            $errors = $this->validate($validateResult->getEntity());
            if ($this->hasErrors($errors)) {
                return call_user_func($onError, $errors);
            } else {
                return call_user_func($onSucess, $validateResult->getEntity());
            }
        }
    }

    /**
     * Procesa los errores
     *
     * @param object $validateResult Validateresultado
     * @param callback $sucess Callback para devolver respuesta exitosa
     * @param callback $error Callback para devolver respuesta fallida
     * @return mixed
     */
    protected function processError($validateResult, $onSucess, $onError)
    {
        if ($validateResult->hasError()) {
            return call_user_func($onError, $validateResult->getErrors());
        }
        return call_user_func($onSucess);
    }

    /**
     * Devuelve un objeto respuesta
     *
     * @param array $metadata Metadatos de la respuesta
     * @param array $result Datos de la respuesta
     * @return object Respuesta
     */
    protected function respuestaData($metadata, $result)
    {
        return new Respuesta($metadata, $result);
    }

    /**
     * Parsea la respuesta que viene de los servidores en formato ARRAY y retorna un ValidateResultado
     *
     * @param array $responseApi
     * @return ValidateResultado
     */
    protected function parseResponse($responseApi)
    {
        if (isset($responseApi['error']['code']) && $responseApi['error']['code'] == 500) {
            return new ValidateResultado(null, $responseApi['error']['exception'][0]['message']);
        }

        if (isset($responseApi['code']) && $responseApi['code'] == 400) {
            return new ValidateResultado(null, $responseApi['userMessage']['errors']);
        }

        if (isset($responseApi['code']) && $responseApi['code'] == 200) {
            return new ValidateResultado($responseApi, []);
        }
    }
}

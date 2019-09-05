<?php

namespace ApiV1Bundle\ApplicationServices;

use ApiV1Bundle\Entity\Response\Respuesta;
use ApiV1Bundle\Entity\ValidateResultado;
use Symfony\Component\DependencyInjection\Container;

/**
 * Class SNTServices
 * @package ApiV1Bundle\ApplicationServices
 */

class SNTServices
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
     * @param string $parameter parametro a obtener
     * @return mixed
     */
    public function getParameter($parameter)
    {
        return $this->container->getParameter($parameter);
    }

    /**
     * Obtener el encoder de passwords
     *
     * @return object
     */
    protected function getSecurityPassword()
    {
        return $this->container->get('security.password_encoder');
    }
    
    
        /**
     * Obtiene el container Redis
     *
     * @return object
     * @throws \Exception
     */
    protected function getContainerRedis()
    {
        return $this->container->get('snc_redis.default');
    }

    /**
     * Valida una entidad que recibe por parámetro
     *
     * @param object $entity Objeto entidad
     * @return array
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
     * @param callback $onSucess Callback para devolver respuesta exitosa
     * @param callback $onError Callback para devolver respuesta fallida
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
     * @param ValidateResultado $validateResult Objeto a verificar si tiene errores
     * @param callback $success Callback para devolver respuesta exitosa
     * @param callback $onError Callback para devolver respuesta fallida
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
     * @param array $metadata metadata de respuesta
     * @param array $result Datos específicos de respuesta
     * @return mixed
     */
    protected function respuestaData($metadata, $result)
    {
        return new Respuesta($metadata, $result);
    }
}

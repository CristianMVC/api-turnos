<?php
namespace TotemV1Bundle\Mocks;

use Symfony\Component\DependencyInjection\Container;

/**
 * Class SNCExternalServiceMock
 * @package TotemV1Bundle\Mocks
 */
class SNCExternalServiceMock
{
    private $host = null;
    private $urls = [];
    private $apiId = [];
    private $keys = [];

    public function __construct(Container $container)
    {
        $config = $container->getParameter('integration');
        $this->host = $config['host'];
        $this->urls = $config['urls'];
        $this->apiId = $config['api_id'];
        $this->keys = $config['keys'];
    }

    /**
     * Creación de una cola en el SNC
     *
     * @param string $url URL
     * @param array $body cuerpo del request
     * @return mixed
     */
    public function post($url, $body = null)
    {
        return $this->getResponse($body);
    }

    /**
     * Modificación de una cola en el SNC
     *
     * @param string $url URL
     * @param array $body cuerpo del request
     * @return mixed
     */
    public function put($url, $body)
    {
        return $this->getResponse($body);
    }

    /**
     * Eliminar una cola del SNC
     *
     * @param string $url URL
     * @param array $body cuerpo del request
     * @return mixed
     */
    public function delete($url, $body)
    {
        return $this->getResponse($body);
    }

    /**
     * Componer una url
     *
     * @param string $name
     * @param string $additional
     * @return NULL|string
     */
    public function getUrl($name, $additional = null, $params = null)
    {
        $url = null;
        if (isset($this->urls[$name])) {
            $url = $this->host . $this->urls[$name];
        }
        if ($url && $additional) {
            if (substr($url, -1) !== '/') {
                $url .= '/';
            }
            $url .= $additional;
        }
        if ($url) {
            $params = $this->getSignedBody($params, false);
        }
        if ($url && $params) {
            if (strpos($url, '?') !== false) {
                $url .= '&';
            } else {
                $url .= '?';
            }
            $url .= http_build_query($params);
        }
        return $url;
    }

    /**
     * Return signed body for test purpose
     * @param array $body cuerpo del request
     * @param boolean $asObject indica si se debe retornar como objecto
     * @return mixed
     */
    public function getTestSignedBody($body, $asObject = true)
    {
        return $this->getSignedBody($body, $asObject);
    }

    /**
     * Headers de la llamada a la API
     *
     * @return array
     */
    private function getHeaders()
    {
        $headers = [
            'Accept' => 'application/json',
            'Content-Type' => 'application/json'
        ];
        return $headers;
    }

    /**
     * Get response
     * @param array $body cuerpo del request
     * @return mixed
     */
    private function getResponse($body = null)
    {
        $response = new \stdClass();
        $response->code = 200;
        $response->body = $this->getSignedBody($body);
        return $response;
    }

    /**
     * Obtenemos el cuerpo del mensaje firmado
     *
     * @param array $body cuerpo del request
     * @param boolean $asObject indica si se debe retornar como objecto
     * @return mixed
     */
    private function getSignedBody($body = null, $asObject = true)
    {
        if (! $body || ! is_array($body)) {
            $body = [];
        }
        $body['api_id'] = $this->apiId['snc'];
        $body['signature'] = $this->sign($body);
        if ($asObject) {
            $body = (object) $body;
        }
        return $body;
    }

    /**
     * Firma digitalmente un request
     *
     * @param array $request request
     * @return string
     */
    private function sign($request)
    {
        $signature = '';
        ksort($request);
        foreach ($request as $key => $value) {
            $signature .= $key . '+' . $value;
        }
        return hash_hmac('sha256', $signature, $this->keys['snc']);
    }
}

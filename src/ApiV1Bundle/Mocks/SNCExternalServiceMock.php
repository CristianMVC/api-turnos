<?php
namespace ApiV1Bundle\Mocks;

use ApiV1Bundle\Entity\ValidateResultado;
use Symfony\Component\DependencyInjection\Container;

/**
 * Class SNCExternalServiceMock
 * @package ApiV1Bundle\Mocks
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
     * método post Mock
     *
     * @param string $url URL
     * @param array $body cuerpo del request
     * @return mixed
     */
    public function post($url, $body = null)
    {
        $urlParts = parse_url($url);
        switch ($urlParts['path']) {
            case '/api/v1.0/integracion/puntosatencion':
            case '/api/v1.0/colas/grupotramite':
                $response = $this->respuestaSuccess();
                break;
            default:
                $response = $this->getResponse($body);
        }
        return $response;
    }

    /**
     * Metodo put Mock
     *
     * @param string $url URL
     * @param array $body cuerpo del request
     * @return mixed
     */
    public function put($url, $body)
    {
        if (  strpos($url,'/api/v1.0/colas/grupotramite') !== false ){
            return $this->respuestaSuccess();
        }
        if (  strpos($url,'/api/v1.0/integracion/puntosatencion') !== false ){
            return $this->respuestaSuccess();
        }
        return $this->getResponse($body);
    }

    /**
     * Metodo Delete Mock
     *
     * @param string $url URL
     * @param array $body cuerpo del request
     * @return mixed
     */
    public function delete($url, $body)
    {
        if (  strpos($url,'/api/v1.0/colas/grupotramite') !== false ){
            return $this->respuestaSuccess();
        }
        if (  strpos($url,'/api/v1.0/integracion/puntosatencion') !== false ){
            return $this->respuestaSuccess();
        }
        return $this->getResponse($body);
    }

    /**
     * Componer una url
     *
     * @param string $name
     * @param string $additional
     * @return NULL|string
     */
    public function getUrl($name, $additional = null, $params = null, $system = null)
    {
        $url = null;
        if (! is_null($system)) {
            if (isset($this->urls[$system][$name])) {
                $url = $this->host[$system] . $this->urls[$system][$name];
            }
        } else {
            //siempre llama a SNC al menos que se especifique lo contrario
            if (isset($this->urls[$name])) {
                $url = $this->host['snc'] . $this->urls[$name];
            }
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
     * @param boolean $asObject indica si se debe retornar como object
     * @return string
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

    /**
     * Retorna un ValidateResultado con estado success
     * @return ValidateResultado
     */
    private function respuestaSuccess()
    {
        $response = json_decode ('{
            "code" : 200,
            "status" : "SUCCESS",
            "userMessage" : "Cola agregada con éxito",
            "devMessage" : "",
            "additional": {
                "id" : 1264      
            }
        }',true);
        return new ValidateResultado($response,[]);
    }
}

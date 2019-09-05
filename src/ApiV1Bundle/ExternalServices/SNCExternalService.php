<?php
namespace ApiV1Bundle\ExternalServices;

use Symfony\Component\DependencyInjection\Container;
use Unirest\Request;
use ApiV1Bundle\Entity\ValidateResultado;

/**
 * Class SNCExternalService
 * @package ApiV1Bundle\ExternalServices
 */
class SNCExternalService
{
    private $host = null;
    private $urls = [];
    private $apiId = [];
    private $keys = [];
    private $logger;

    /**
     * SNCExternalService constructor.
     * @param Container $container
     * @throws \Exception
     */
    public function __construct(Container $container)
    {
        $config = $container->getParameter('integration');
        $this->host = $config['host'];
        $this->urls = $config['urls'];
        $this->apiId = $config['api_id'];
        $this->keys = $config['keys'];
        $this->logger = $container->get('logger');
    }

    /**
     * Método post
     *
     * @param string $url URL
     * @param array $body cuerpo del request
     * @return mixed
     */
    public function post($url, $body = null)
    {
        $request = $this->getSignedBody($body);
        return $this->parseResponse(Request::post($url, $this->getHeaders(), $request));
    }

    /**
     * Método put
     *
     * @param string $url URL
     * @param array $body cuerpo del request
     * @return mixed
     */
    public function put($url, $body)
    {
        $request = $this->getSignedBody($body);
        return $this->parseResponse(Request::put($url, $this->getHeaders(), $request));
    }

    /**
     * Método DELETE
     *
     * @param string $url URL
     * @param array $body cuerpo del request
     * @return mixed
     */
    public function delete($url, $body)
    {
        $request = $this->getSignedBody($body);
        return $this->parseResponse(Request::delete($url, $this->getHeaders(), $request));
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
     * Obtenemos el cuerpo del mensaje firmado
     *
     * @param array $body cuerpo del request
     * @param boolean $asJson indica si formato Json
     * @return array
     */
    private function getSignedBody($body = null, $asJson = true)
    {
        if (! $body || ! is_array($body)) {
            $body = [];
        }
        $body['api_id'] = $this->apiId['snt'];
        $body['signature'] = $this->sign($body);
        if ($asJson) {
            $body = Request\Body::json($body);
        }
        return $body;
    }

    /**
     * Firma digitalmente un request
     *
     * @param array $request Request
     * @return string
     */
    private function sign($request)
    {
        $signature = $this->arrayToSignature($request);
        return hash_hmac('sha256', $signature, $this->keys['snt']);
    }

    /**
     * función recursiva que permite recorrer un arreglo y pasarlo a una cadena
     *
     * @param array $arreglo arreglo, normalmente el request
     * @return string
     */
    public function arrayToSignature($arreglo)
    {
        $cadena = '';
        ksort($arreglo);
        foreach ($arreglo as $key => $value) {
            if (is_array($value)) {
                if (count($value) != count($value, COUNT_RECURSIVE)){
                    $value = $this->arrayToSignature($value);
                }else{
                    ksort($value);
                    $value = implode(':', $value);
                }
            }
            $cadena .= $key . '+' . $value;
        }
        return $cadena;
    }

    /**
     * Parsea la respuesta que viene de los servidores y retorna un array
     *
     * @param $responseApi
     * @return array
     */
    public function parseResponseGet($responseApi)
    {
        $responseApi = $this->objectToArray($responseApi);
        if (isset($responseApi['code']) && $responseApi['code'] == 200) {
            return $responseApi["body"];
        }
        if (isset($responseApi['code']) && $responseApi['code'] == 500) {
            $this->logger->error("code:" . $responseApi['code'] . " | " . $responseApi["body"]['error']['exception'][0]['message'] );
            return [];
        }
        if (isset($responseApi['code']) && $responseApi['code'] == 400) {
            $this->logger->error("code:" . $responseApi['code'] . " | " . $responseApi["body"]['userMessage']['errors']);
            return [];
        }
        $this->logger->error("code:" . $responseApi['code'] . " | " . implode(" | ",$responseApi['headers']) );
        return [];
    }

    /**
     * Parsea la respuesta que viene de los servidores en object y retorna un ValidateResultado
     *
     * @param $responseApi
     * @return ValidateResultado
     */
    public function parseResponse($responseApi)
    {
        $responseApi = $this->objectToArray($responseApi);
        if (isset($responseApi['code']) && $responseApi['code'] == 200) {
            return new ValidateResultado($responseApi["body"], []);
        }
        if (isset($responseApi['code']) && $responseApi['code'] == 500) {
            $this->logger->error("code:" . $responseApi['code'] . " | " . $responseApi["body"]['error']['exception'][0]['message'] );
            return new ValidateResultado(null, $responseApi["body"]['error']['message']);
        }
        if (isset($responseApi['code']) && $responseApi['code'] == 400) {
            return new ValidateResultado(null, $responseApi["body"]['userMessage']['errors']);
        }
        $this->logger->error("code:" . $responseApi['code'] . " | " . implode(" | ",$responseApi['headers']) );
        return new ValidateResultado(null,"Error de comunicación entre apis.");
    }

    /**
     * Convert object to array
     *
     * @param object $response
     * @return array
     */
    private function objectToArray($response)
    {
        if ($response) {
            $result = [];
            foreach ($response as $key => $value) {
                if (is_object($value) || is_array($value)) {
                    $result[$key] = $this->objectToArray($value);
                } else {
                    $result[$key] = $value;
                }
            }
            return $result;
        }
        return null;
    }
}

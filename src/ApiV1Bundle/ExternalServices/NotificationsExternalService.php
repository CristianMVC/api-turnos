<?php
namespace ApiV1Bundle\ExternalServices;

use Symfony\Component\DependencyInjection\Container;
use Unirest\Request;

/**
 * Class NotificationsExternalService
 * @package ApiV1Bundle\ExternalServices
 */
class NotificationsExternalService
{
    private $container;
    private $host;
    private $user;
    private $pass;
    private $token;
    private $from;
    private $subject;
    private $templates;
    private $urls;
    private $batchLimit = 15;

    /**
     * NotificationsExternalService constructor.
     * @param Container $container
     */
    public function __construct(Container $container)
    {
        $config = $container->getParameter('notificaciones');
        $this->container = $container;
        $this->host = $config['host'];
        $this->user = $config['user'];
        $this->pass = $config['pass'];
        $this->token = $config['token'];
        $this->from = $config['from'];
        $this->subject = $config['subject'];
        $this->templates = $config['templates'];
        $this->urls = $config['urls'];
        $this->batchLimit = $config['batch_limit'];
        $this->environment = $this->getEnvironment();
    }

    /**
     * Obtiene el ambiente en que corre la aplicación
     *
     * @return string
     */
    private function getEnvironment()
    {
        return $this->container->get('kernel')->getEnvironment();
    }

    /**
     * Obtenemos la configuración del servicio
     */
    public function getConfig()
    {
        return [
            'host' => $this->host,
            'user' => $this->user,
            'pass' => $this->pass,
            'token' => $this->token,
            'urls' => $this->urls,
            'templates' => $this->templates
        ];
    }

    /**
     * Generate API token
     *
     * @return mixed
     */
    public function getToken()
    {
        $url = $this->host . $this->urls['auth'];
        $body = Request\Body::json([
            'username' => $this->user,
            'password' => $this->pass
        ]);
        $response = Request::post($url, $this->getHeaders(), $body);
        $errors = $this->checkErrors($response->body);
        if ($errors) {
            return $errors;
        }
        return $response->body;
    }

    /**
     * Obtener la lista de templates
     *
     * @param string $name nombre de template
     * @return mixed
     */
    public function getTemplate($name = null)
    {
        $url = $this->host . $this->urls['templates'];
        if ($name) {
            $url .= '/' . $name;
        }
        $response = Request::get($url, $this->getHeaders(true));
        $errors = $this->checkErrors($response->body);
        if ($errors) {
            return $errors;
        }
        return $response->body;
    }

    /**
     * Test del template
     *
     * @param string $name nombre de template
     * @param array $data contenido
     * @return mixed
     */
    public function testTemplate($name, $data)
    {
        $url = $this->host . sprintf($this->urls['template_test'], $name);
        $response = Request::post($url, $this->getHeaders(true), $data);
        $errors = $this->checkErrors($response->body);
        if ($errors) {
            return $errors;
        }
        return $response->body;
    }

    /**
     * Crear template
     *
     * @param string $name nombre de template
     * @param string $template template
     * @return mixed
     */
    public function crearTemplate($name, $template)
    {
        $url = $this->host . $this->urls['templates'];
        $body = Request\Body::json([
            'name' => $name,
            'template' => $template
        ]);
        $response = Request::post($url, $this->getHeaders(true), $body);
        $errors = $this->checkErrors($response->body);
        if ($errors) {
            return $errors;
        }
        return $response->body;
    }

    /**
     * Editar un template
     *
     * @param string $name nombre de template
     * @param string $template template
     * @return mixed
     */
    public function editarTemplate($name, $template)
    {
        $url = $this->host . $this->urls['templates'] . '/' . $name;
        $body = Request\Body::json([
            'template' => $template
        ]);
        $response = Request::put($url, $this->getHeaders(true), $body);
        $errors = $this->checkErrors($response->body);
        if ($errors) {
            return $errors;
        }
        return $response->body;
    }

    /**
     * Enviar una notificacion
     *
     * @param string $template Template
     * @param string $email Email del usuario
     * @param string $cuil Cuil del ciudadano
     * @param array $params arreglo con los datos
     * @return mixed
     */
    public function enviarNotificacion($template, $email, $cuil, $params)
    {
        $url = $this->host . $this->urls['notifications'];
        $body = Request\Body::json([
            'recipients' => [
                [
                    'content' => [
                        'email' => [
                            'params' => $params,
                            'from' => $this->from,
                            'from_text' => 'Mi Argentina', // TODO: hacer que lo tome desde config
                            'to' => $email,
                            'subject' => $this->subject,
                            'template' => $template
                        ],
                    ],
                    'cuil' => $cuil,
                    'force' => true
                ]
            ]
        ]);

        $response = Request::post($url, $this->getHeaders(true), $body);
        $errors = $this->checkErrors($response->body);
        if ($errors) {
            return $errors;
        }

        if ($response->code ==500){
            return $errors[] = "No se ha podido enviar el mail.";
        }
        return $response->body;
    }

    /**
     * Enviar notificaciones en batch
     *
     * @param string $template
     * @param array $batch
     * @return mixed
     */
    public function enviarNotificacionBatch($template, $batch)
    {
        $url = $this->host . $this->urls['notifications'];
        $body = [
            'recipients' => []
        ];

        foreach ($batch as $entry) {
            $body['recipients'][] = [
                'content' => [
                    'email' => [
                        'params' => $entry['params'],
                        'from' => $this->from,
                        'from_text' => 'Mi Argentina', // TODO: hacer que lo tome desde config
                        'to' => $entry['email'],
                        'subject' => $this->subject,
                        'template' => $template
                    ],
                ],
                'cuil' => $entry['cuil'],
                'force' => true
            ];
        }

        $body = Request\Body::json($body);
        $response = Request::post($url, $this->getHeaders(true), $body);
        $errors = $this->checkErrors($response->body);
        if ($errors) {
            return $errors;
        }
        return $response->body;
    }

    /**
     * Obtenemos la URL que va en el mail
     *
     * @param string $index
     * @return mixed
     */
    public function getEmailUrl($index)
    {
        $url = $this->urls['base_url'];
        if (isset($this->urls['turno'][$index])) {
            $url .= $this->urls['turno'][$index];
        }
        return $url;
    }

    /**
     * Verificar estado de una notificación
     *
     * @param integer $id identificador
     * @return mixed
     */
    public function verificarNotificacion($id)
    {
        $url = $this->host . $this->urls['notifications'] . $id;
        $response = Request::get($url, $this->getHeaders(true));
        $errors = $this->checkErrors($response->body);
        if ($errors) {
            return $errors;
        }
        return $response->body;
    }

    /**
     * Devuelve el nombre del template que se usa
     *
     * @param string $name
     * @return string
     */
    public function getEmailTemplate($name)
    {
        return $this->templates[$name];
    }

    /**
     * Devolvemos el límite de mails por batch
     *
     * @return number
     */
    public function getBatchLimit()
    {
        return $this->batchLimit;
    }

    /**
     * Check errors
     *
     * @param $body
     * @return array|NULL
     */
    private function checkErrors($body)
    {
        if (property_exists($body, 'non_field_errors')) {
            return $body->non_field_errors;
        }
        return null;
    }

    /**
     * Headers de la llamada a la API
     *
     * @param string $token
     * @return []
     */
    private function getHeaders($token = null)
    {
        $headers = [];
        $headers['Accept'] = 'application/json';
        $headers['Content-Type'] = 'application/json';
        if ($token) {
            $headers['Authorization'] = 'Token ' . $this->token;
        }
        return $headers;
    }
}

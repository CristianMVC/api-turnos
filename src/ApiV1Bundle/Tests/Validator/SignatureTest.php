<?php
namespace ApiV1Bundle\Tests\Validator;

use ApiV1Bundle\ExternalServices\SNCExternalService;
use ApiV1Bundle\Entity\Validator\CommunicationValidator;

/**
 * Class SignatureTest
 * @package ApiV1Bundle\Tests\Validator
 */
class SignatureTest extends ValidatorTestCase
{
    private $externalService;
    private $apiId = [];
    private $keys = [];
    private $communicationValidator;

    public function setUp()
    {
        self::bootKernel();
        $container = static::$kernel->getContainer();
        $this->externalService = new SNCExternalService($container);
        $this->communicationValidator = new CommunicationValidator($container);
        $config = $container->getParameter('integration');
        $this->apiId = $config['api_id'];
        $this->keys = $config['keys'];

    }
    /**
     * test de validación de firma digital
     * Se valida la firma con la key snc
     */
    public function testSignature()
    {
        $request = array(
            "puntoatencion" => "1",
            "grupo_tramite" => "2",
            "otroNivel" => array(
                "ventanilla" => array(
                    "id" => "133",
                    "nombre" => "ventanilla 1"
                ),
                "carteleras" => array(
                    0 => array(
                        "id" => "4587",
                        "nombre" => "Cartelera de prueba"
                    ),
                    1 => array(
                        "id" => "1206",
                        "nombre" => "Cartelera pasillo"
                    )
                )
            ),
            "api_id" => $this->apiId['snc']
        );
        $signature = $this->externalService->arrayToSignature($request);
        $request["signature"] = hash_hmac('sha256', $signature , $this->keys['snc']);
        $validateResult = $this->communicationValidator->validateSNCRequest($request);
        $this->assertNotEquals(true, $validateResult->hasError());
    }

}

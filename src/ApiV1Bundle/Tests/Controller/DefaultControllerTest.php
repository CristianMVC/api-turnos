<?php
namespace ApiV1Bundle\Tests\Controller;

/**
 * Class DefaultControllerTest
 * @package ApiV1Bundle\Tests\Controller
 */
class DefaultControllerTest extends ControllerTestCase
{
    /**
     *  Test index
     */
    public function testIndex()
    {
        $client = static::createClient();
        $client->followRedirects();
        $client->request('GET', '/api/v1.0');
        $this->assertEquals(200, $client->getResponse()->getStatusCode());
    }
}

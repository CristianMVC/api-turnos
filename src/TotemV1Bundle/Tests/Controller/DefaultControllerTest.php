<?php

namespace TotemV1Bundle\Tests\Controller;

use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;

/**
 * Class DefaultControllerTest
 * @package TotemV1Bundle\Tests\Controller
 */
class DefaultControllerTest extends WebTestCase
{
    /**
     * Test index
     */
    public function testIndex()
    {
        $client = static::createClient();
        $client->request('GET', '/');
        $this->assertContains('Welcome!', $client->getResponse()->getContent());
    }
}

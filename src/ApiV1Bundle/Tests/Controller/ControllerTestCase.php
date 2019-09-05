<?php

namespace ApiV1Bundle\Tests\Controller;

use MyProject\Container;
use Symfony\Bundle\FrameworkBundle\Test\KernelTestCase;
use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;
use Symfony\Bundle\FrameworkBundle\Console\Application;
use Symfony\Component\Console\Input\ArrayInput;
use Symfony\Component\Console\Output\BufferedOutput;

/**
 * Class ControllerTestCase
 * @package ApiV1Bundle\Tests\Controller
 */
class ControllerTestCase extends WebTestCase
{
    /** @var \Symfony\Component\DependencyInjection\Container **/
    private $container;

    /**
     * @var \Doctrine\ORM\EntityManager
     */
    protected $entityManager;

    /**
     * {@inheritDoc}
     */
    protected function setUp()
    {
        self::bootKernel();
        $this->container = static::$kernel->getContainer();
        $this->entityManager = static::$kernel->getContainer()
            ->get('doctrine')
            ->getManager();
    }

    /**
     * {@inheritDoc}
     */
    protected function tearDown()
    {
        $this->entityManager->close();
        $this->entityManager = null; // avoid memory leaks
    }

    /**
     * {@inheritDoc}
     */
    public static function tearDownAfterClass()
    {
    }

    /**
     * Obtenemos el contenedor
     * @return object
     */
    protected function getContainer()
    {
        return $this->container;
    }

    /**
     * Test login user
     */
    protected function loginTestUser()
    {
        $client = static::createClient();
        $client->followRedirects();
        $params = [
            'username' => 'test@test.com',
            'password' => 'test'
        ];
        $client->request('POST', '/api/v1.0/auth/login', $params);
        // content
        $content = json_decode($client->getResponse()->getContent(), true);
        return $content['token'];
    }

    /**
     * Test login user organismo
     */
    protected function loginOrganismoUser()
    {
        $client = static::createClient();
        $client->followRedirects();
        $params = [
            'username' => 'organismo@mail.com',
            'password' => 'test'
        ];
        $client->request('POST', '/api/v1.0/auth/login', $params);
        // content
        $content = json_decode($client->getResponse()->getContent(), true);
        return $content['token'];
    }

    /**
     * Test login user area
     */
    protected function loginAreaUser()
    {
        $client = static::createClient();
        $client->followRedirects();
        $params = [
            'username' => 'area@mail.com',
            'password' => 'test'
        ];
        $client->request('POST', '/api/v1.0/auth/login', $params);
        // content
        $content = json_decode($client->getResponse()->getContent(), true);
        return $content['token'];
    }

    /**
     * Test login user responsable punto de atención
     */
    protected function loginPuntoAtencionUser()
    {
        $client = static::createClient();
        $client->followRedirects();
        $params = [
            'username' => 'pda@mail.com',
            'password' => 'test'
        ];
        $client->request('POST', '/api/v1.0/auth/login', $params);
        // content
        $content = json_decode($client->getResponse()->getContent(), true);
        return $content['token'];
    }
}

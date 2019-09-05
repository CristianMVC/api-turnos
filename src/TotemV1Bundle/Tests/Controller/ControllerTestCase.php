<?php

namespace TotemV1Bundle\Tests\Controller;

use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;
use Symfony\Bundle\FrameworkBundle\Console\Application;
use Symfony\Component\Console\Input\ArrayInput;
use Symfony\Component\Console\Output\BufferedOutput;
use Symfony\Component\DependencyInjection\Container;

/**
 * Class ControllerTestCase
 * @package TotemV1Bundle\Tests\Controller
 */
class ControllerTestCase extends WebTestCase
{
    /** @var Container */
    private $container;

    /**
     * {@inheritDoc}
     */
    protected function setUp()
    {
        self::bootKernel();
        $this->container = static::$kernel->getContainer();
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
}

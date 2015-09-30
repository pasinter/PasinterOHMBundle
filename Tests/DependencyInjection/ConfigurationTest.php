<?php

namespace Pasinter\Bundle\RedisOHMBundle\Tests\DependencyInjection;

use Pasinter\Bundle\RedisOHMBundle\DependencyInjection\Configuration;
use Symfony\Component\Config\Definition\Processor;

class ConfigurationTest extends \PHPUnit_Framework_TestCase
{
    public function testDefaults()
    {
        $processor = new Processor();
        $configuration = new Configuration(false);
        $options = $processor->processConfiguration($configuration, array());
        $defaults = array(
            'entity_managers' => array(),
            'connections' => array(),
        );
        $this->assertEquals($defaults, $options);
    }
}


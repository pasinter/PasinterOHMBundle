<?php

namespace Pasinter\Bundle\RedisOHMBundle\Tests\DependencyInjection;

use Pasinter\Bundle\RedisOHMBundle\DependencyInjection\PasinterRedisOHMExtension;
use PHPUnit_Framework_TestCase;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\DependencyInjection\ParameterBag\ParameterBag;

class PasinterRedisOHMExtensionTest extends PHPUnit_Framework_TestCase
{
    public function test_getObjectManagerElementName()
    {
        $container = $this->getContainer();
        $extension = new PasinterRedisOHMExtension();
        $container->registerExtension($extension);
        $extension->load([], $container);
        
        $this->assertTrue($container->has('pasinter_ohm'));
    }
    
    private function getContainer()
    {
        $map = array();
        return new ContainerBuilder(new ParameterBag(array(
            'kernel.debug' => false,
            'kernel.bundles' => $map,
            'kernel.cache_dir' => sys_get_temp_dir(),
            'kernel.environment' => 'test',
            'kernel.root_dir' => __DIR__.'/../../', // src dir
        )));
    }
}


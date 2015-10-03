<?php

namespace Pasinter\Bundle\RedisOHMBundle\DependencyInjection;

use Symfony\Bridge\Doctrine\DependencyInjection\AbstractDoctrineExtension;
use Symfony\Component\Config\FileLocator;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\DependencyInjection\Loader\XmlFileLoader;

class PasinterRedisOHMExtension extends AbstractDoctrineExtension
{
    /**
     * @ineritdoc
     */
    public function load(array $config, ContainerBuilder $container)
    {
        // Load RedisOHMBundle/Resources/config/redis-ohm.xml
        $loader = new XmlFileLoader($container, new FileLocator(__DIR__.'/../Resources/config'));
        $loader->load('redis-ohm.xml');
        
        $configuration = new Configuration();
        $config = $this->processConfiguration($configuration, $config);
    }
    
    /**
     * @ineritdoc
     */
    protected function getMappingObjectDefaultName()
    {
        return 'Entity';
    }

    /**
     * @ineritdoc
     */
    protected function getMappingResourceConfigDirectory()
    {
        return 'Resources/config/redis-ohm';
    }

    /**
     * @ineritdoc
     */
    protected function getMappingResourceExtension()
    {
        return 'redis';
    }

    /**
     * @ineritdoc
     */
    protected function getObjectManagerElementName($name)
    {
        return 'pasinter_redis.ohm.' . $name;
    }
}


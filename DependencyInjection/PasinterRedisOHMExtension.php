<?php

namespace Pasinter\Bundle\RedisOHMBundle\DependencyInjection;

use Symfony\Bridge\Doctrine\DependencyInjection\AbstractDoctrineExtension;

class PasinterRedisOHMExtension extends AbstractDoctrineExtension
{
    /**
     * @ineritdoc
     */
    public function load(array $config, \Symfony\Component\DependencyInjection\ContainerBuilder $container)
    {
        // Load RedisOHMBundle/Resources/config/redis-ohm.xml
        $loader = new XmlFileLoader($container, new FileLocator(__DIR__.'/../Resources/config'));
        $loader->load('redis-ohm.xml');
        
        $configuration = new Configuration();
        $config = $this->processConfiguration($configuration, $configs);
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


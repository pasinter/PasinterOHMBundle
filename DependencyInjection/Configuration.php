<?php

namespace Pasinter\Bundle\RedisOHMBundle\DependencyInjection;

use Symfony\Component\Config\Definition\Builder\ArrayNodeDefinition;
use Symfony\Component\Config\Definition\Builder\TreeBuilder;
use Symfony\Component\Config\Definition\ConfigurationInterface;

class Configuration implements ConfigurationInterface
{
    /**
     * Generates the configuration tree builder.
     *
     * @return TreeBuilder
     */
    public function getConfigTreeBuilder()
    {
        $treeBuilder = new TreeBuilder();
        $rootNode = $treeBuilder->root('pasinter_redis');
        
        $this->addOhmSection($rootNode);
        $this->addStorageSection($rootNode);
        
        return $treeBuilder;
    }
    
    /**
     * 
     * @param ArrayNodeDefinition $node
     */
    private function addStorageSection(ArrayNodeDefinition $node)
    {
        $node
            ->children()
            ->arrayNode('storage')->addDefaultsIfNotSet()
                ->children()
                    ->scalarNode('default_connection')->defaultValue('default')->end()
                    ->append($this->addConnectionsSection())
                ->end()
            ->end()
            ->end()
        ;
    }
    
    /**
     * Adds the "connections" config section.
     *
     */
    private function addConnectionsSection()
    {
        $treeBuilder = new TreeBuilder();
        $node = $treeBuilder->root('connections');
        /** @var $connectionNode ArrayNodeDefinition */
        $connectionNode = $node
            ->requiresAtLeastOneElement()
            ->useAttributeAsKey('name')
            ->prototype('array')
        ;

        $connectionNode
            ->children()
                ->scalarNode('dsn')->defaultValue('redis://localhost')->end()
                ->scalarNode('storage_class')->defaultValue('Pasinter\OHM\Storage\RedisStorage')->end()
                ->arrayNode('options')->addDefaultsIfNotSet()
                    ->performNoDeepMerging()
                    ->children()
                        ->scalarNode('profile')->defaultValue('2.0')->end()
                        ->integerNode('connection_timeout')->defaultNull()->end()
                        ->integerNode('retry_interval')->defaultNull()->end()
                    ->end()
                ->end()
                ->booleanNode('logging')->defaultFalse()->end()
                ->booleanNode('profiling')->defaultFalse()->end()
            ->end()
        ;

        return $node;
    }
    
    /**
     * 
     * @param ArrayNodeDefinition $node
     */
    private function addOhmSection(ArrayNodeDefinition $node)
    {
        $node
            ->children()
                ->arrayNode('ohm')->addDefaultsIfNotSet()
                    ->beforeNormalization()
                        ->ifTrue(function ($v) { return null === $v || (is_array($v) && !array_key_exists('entity_managers', $v) && !array_key_exists('entity_manager', $v)); })
                        ->then(function ($v) {
                            $v = (array) $v;
                            // Key that should not be rewritten to the connection config
                            $excludedKeys = [
                                'default_entity_manager' => true,
                                'auto_generate_proxy_classes' => true,
                                'proxy_dir' => true,
                                'proxy_namespace' => true
                            ];
                            $entityManagers = [];
                            foreach ($v as $key => $value) {
                                if (isset($excludedKeys[$key])) {
                                    continue;
                                }
                                $entityManagers[$key] = $v[$key];
                                unset($v[$key]);
                            }
                            $v['default_entity_manager'] = isset($v['default_entity_manager']) ? (string) $v['default_entity_manager'] : 'default';
                            $v['entity_managers'] = [$v['default_entity_manager'] => $entityManagers];
                            return $v;
                        })
                    ->end()
                    ->children()
                        ->scalarNode('default_entity_manager')->defaultValue('default')->end()
                        ->booleanNode('auto_generate_proxy_classes')->defaultFalse()->end()
                        ->scalarNode('proxy_dir')->defaultValue('%kernel.cache_dir%/pasinter/RedisOHMProxies')->end()
                        ->scalarNode('proxy_namespace')->defaultValue('RedisOHMProxies')->end()
                    ->end()
                    ->fixXmlConfig('entity_manager')
                    ->append($this->getEntityManagersNode())
                ->end()
            ->end()
        ;
    }
    
    /**
     * 
     * @return ArrayNodeDefinition
     */
    private function getEntityManagersNode()
    {
        $treeBuilder = new TreeBuilder();
        $node = $treeBuilder->root('entity_managers');
        $node
            ->requiresAtLeastOneElement()
            ->useAttributeAsKey('name')
            ->prototype('array')
                ->addDefaultsIfNotSet()
                ->append($this->getCacheDriverNode('metadata_cache_driver'))
                ->append($this->getCacheDriverNode('query_cache_driver'))
                ->append($this->getCacheDriverNode('result_cache_driver'))
                ->children()
                    ->scalarNode('class_metadata_factory_name')->defaultValue('Pasinter\OHM\Mapping\ClassMetadataFactory')->end()
                    ->scalarNode('default_repository_class')->defaultValue('Pasinter\OHM\EntityRepository')->end()
                    ->scalarNode('repository_factory')->defaultNull()->end()
                ->end()
            ->end()
        ;
        return $node;
    }
    
    /**
     * 
     * @return ArrayNodeDefinition
     */
    private function getCacheDriverNode($name)
    {
        $treeBuilder = new TreeBuilder();
        $node = $treeBuilder->root($name);
        $node
            ->addDefaultsIfNotSet()
            ->beforeNormalization()
            ->ifString()
            ->then(function ($v) {
            return ['type' => $v];
        })
            ->end()
            ->children()
                ->scalarNode('type')->defaultValue('array')->end()
                ->scalarNode('host')->end()
                ->scalarNode('port')->end()
                ->scalarNode('instance_class')->end()
                ->scalarNode('class')->end()
                ->scalarNode('id')->end()
                ->scalarNode('namespace')->defaultNull()->end()
            ->end();
        return $node;
    }
}


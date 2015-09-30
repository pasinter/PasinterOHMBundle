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
        
        $this->addOrmSection($rootNode);
        $this->addConnectionsSection($rootNode);
        
        return $treeBuilder;
    }
    
    /**
     * Adds the "connections" config section.
     *
     * @param ArrayNodeDefinition $rootNode
     */
    private function addConnectionsSection(ArrayNodeDefinition $rootNode)
    {
        $rootNode
            ->fixXmlConfig('connection')
            ->children()
                ->arrayNode('connections')
                    ->useAttributeAsKey('id')
                    ->prototype('array')
                        ->performNoDeepMerging()
                        ->children()
                            ->scalarNode('dsn')->end()
                            ->arrayNode('options')
                                ->performNoDeepMerging()
                                ->children()
                                    ->integerNode('connectTimeoutMS')->end()
                                    ->integerNode('retryIntervalMS')->end()
                                ->end()
                            ->end()
                        ->end()
                    ->end()
                ->end()
            ->end()
        ;
    }
    
    private function addOrmSection(ArrayNodeDefinition $node)
    {
        $node
            ->children()
                ->arrayNode('orm')
                    ->beforeNormalization()
                        ->ifTrue(function ($v) { return null === $v || (is_array($v) && !array_key_exists('entity_managers', $v) && !array_key_exists('entity_manager', $v)); })
                        ->then(function ($v) {
                            $v = (array) $v;
                            // Key that should not be rewritten to the connection config
                            $excludedKeys = array(
                                'default_entity_manager' => true,
                                'auto_generate_proxy_classes' => true,
                                'proxy_dir' => true,
                                'proxy_namespace' => true
                            );
                            $entityManagers = array();
                            foreach ($v as $key => $value) {
                                if (isset($excludedKeys[$key])) {
                                    continue;
                                }
                                $documentManagers[$key] = $v[$key];
                                unset($v[$key]);
                            }
                            $v['default_entity_manager'] = isset($v['default_entity_manager']) ? (string) $v['default_entity_manager'] : 'default';
                            $v['entity_managers'] = array($v['default_entity_manager'] => $documentManagers);
                            return $v;
                        })
                    ->end()
                    ->children()
                        ->scalarNode('default_entity_manager')->end()
                        ->booleanNode('auto_generate_proxy_classes')->defaultFalse()->end()
                        ->scalarNode('proxy_dir')->defaultValue('%kernel.cache_dir%/pasinter/RedisOHMProxies')->end()
                        ->scalarNode('proxy_namespace')->defaultValue('RedisOHMProxies')->end()
                    ->end()
                    ->fixXmlConfig('entity_manager')
                    ->append($this->getOrmDocumentManagersNode())
                ->end()
            ->end()
        ;
    }
    
    private function getOrmDocumentManagersNode()
    {
        $treeBuilder = new TreeBuilder();
        $node = $treeBuilder->root('entity_managers');
        $node
            ->requiresAtLeastOneElement()
            ->useAttributeAsKey('name')
            ->prototype('array')
                ->addDefaultsIfNotSet()
                ->append($this->getOdmCacheDriverNode('metadata_cache_driver'))
                ->children()
                    ->scalarNode('session')->end()
                    ->scalarNode('configuration_id')->end()
                    ->scalarNode('class_metadata_factory_name')->defaultValue('Pasinter\OHM\Mapping\ClassMetadataFactory')->end()
                    ->scalarNode('auto_mapping')->defaultFalse()->end()
                    ->scalarNode('default_repository_class')->defaultValue('Pasinter\OHM\EntityRepository')->end()
                    ->scalarNode('repository_factory')->defaultNull()->end()
                ->end()
                ->fixXmlConfig('mapping')
                ->children()
                    ->arrayNode('mappings')
                        ->useAttributeAsKey('name')
                        ->prototype('array')
                            ->beforeNormalization()
                                ->ifString()
                                ->then(function ($v) { return array('type' => $v); })
                            ->end()
                            ->treatNullLike(array())
                            ->treatFalseLike(array('mapping' => false))
                            ->performNoDeepMerging()
                            ->children()
                                ->scalarNode('mapping')->defaultValue(true)->end()
                                ->scalarNode('type')->end()
                                ->scalarNode('dir')->end()
                                ->scalarNode('alias')->end()
                                ->scalarNode('prefix')->end()
                                ->booleanNode('is_bundle')->end()
                            ->end()
                        ->end()
                    ->end()
                ->end()
            ->end()
        ;
        return $node;
    }
    
    private function getOdmCacheDriverNode($name)
    {
        $treeBuilder = new TreeBuilder();
        $node = $treeBuilder->root($name);
        $node
            ->addDefaultsIfNotSet()
            ->beforeNormalization()
            ->ifString()
            ->then(function ($v) {
            return array('type' => $v);
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


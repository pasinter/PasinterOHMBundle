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
        $options = $processor->processConfiguration($configuration, []);
        $defaults = [
            'storage' => [
                'default_connection' => 'default',
                'connections' => []
            ],
            'ohm' => [
                'default_entity_manager' => 'default',
                'auto_generate_proxy_classes' => false,
                'entity_managers' => [],
                'proxy_dir' => '%kernel.cache_dir%/pasinter/RedisOHMProxies',
                'proxy_namespace' => 'RedisOHMProxies'
            ],
        ];
        
        $this->assertEquals($defaults, $options);
    }
    
    public function testMinimalConfiguration()
    {
        $processor = new Processor();
        $configuration = new Configuration(false);
        $options = $processor->processConfiguration($configuration, [
            'pasinter_redis' => [
                'storage' => [
                    'connections' => [
                        'default' => []
                    ]
                ],
                'ohm' => [
                    'entity_managers' => [
                        'default' => []
                    ]
                ]
            ]
        ]);
        $defaults = [
            'storage' => [
                'default_connection' => 'default',
                'connections' => [
                    'default' => [
                        'dsn' => 'redis://localhost',
                        'storage_class' => 'Pasinter\OHM\Storage\RedisStorage',
                        'logging' => false,
                        'profiling' => false
                    ]
                ]
            ],
            'ohm' => [
                'auto_generate_proxy_classes' => false,
                'entity_managers' => [
                    'default' => [
                        'metadata_cache_driver' => [
                            'type' => 'array',
                            'namespace' => null
                        ],
                        'query_cache_driver' => [
                            'type' => 'array',
                            'namespace' => null
                        ],
                        'result_cache_driver' => [
                            'type' => 'array',
                            'namespace' => null
                        ],
                        'class_metadata_factory_name' => 'Pasinter\OHM\Mapping\ClassMetadataFactory',
                        'default_repository_class' => 'Pasinter\OHM\EntityRepository',
                        'repository_factory' => null
                    ]
                ],
                'proxy_dir' => '%kernel.cache_dir%/pasinter/RedisOHMProxies',
                'proxy_namespace' => 'RedisOHMProxies',
                'default_entity_manager' => 'default'
            ],
        ];
        
        $this->assertEquals($defaults, $options);
    }
}


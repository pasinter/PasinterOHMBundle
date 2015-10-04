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
                        'profiling' => false,
                        'options' => [
                            'profile' => '2.0',
                            'connection_timeout' => null,
                            'retry_interval' => null
                        ]
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
    
    public function testFullConfiguration()
    {
        $storageMock = $this->getMock('Pasinter\OHM\Storage\StorageInterface');
        $classMetadataFactoryMock = $this->getMock('Doctrine\Common\Persistence\Mapping\ClassMetadataFactory');
        $entityRepositoryMock = $this->getMock('Doctrine\Common\Persistence\ObjectRepository');
        
        $processor = new Processor();
        $configuration = new Configuration(false);
        $options = $processor->processConfiguration($configuration, [
            'pasinter_redis' => [
                'storage' => [
                    'default_connection' => 'custom',
                    'connections' => [
                        'default' => [],
                        'custom' => [
                            'dsn' => 'redis://127.0.0.1/2',
                            'storage_class' => get_class($storageMock),
                            'logging' => true,
                            'profiling' => true,
                            'options' => [
                                'profile' => '2.6',
                                'connection_timeout' => 1,
                                'retry_interval' => 2
                            ]
                        ]
                    ]
                ],
                'ohm' => [
                    'default_entity_manager' => 'custom',
                    'entity_managers' => [
                        'default' => [],
                        'custom' => [
                            'metadata_cache_driver' => [
                                'type' => 'redis'
                            ],
                            'query_cache_driver' => [
                                'type' => 'redis'
                            ],
                            'result_cache_driver' => [
                                'type' => 'redis'
                            ],
                            'class_metadata_factory_name' => get_class($classMetadataFactoryMock),
                            'default_repository_class' => get_class($entityRepositoryMock)
                        ]
                    ],
                    'proxy_dir' => '%kernel.cache_dir%/Another/Dir',
                    'proxy_namespace' => 'Another\Proxies\Namespace',
                ]
            ]
        ]);
        $defaults = [
            'storage' => [
                'default_connection' => 'custom',
                'connections' => [
                    'default' => [
                        'dsn' => 'redis://localhost',
                        'storage_class' => 'Pasinter\OHM\Storage\RedisStorage',
                        'logging' => false,
                        'profiling' => false,
                        'options' => [
                            'profile' => '2.0',
                            'connection_timeout' => null,
                            'retry_interval' => null
                        ]
                    ],
                    'custom' => [
                        'dsn' => 'redis://127.0.0.1/2',
                        'storage_class' => get_class($storageMock),
                        'logging' => true,
                        'profiling' => true,
                        'options' => [
                            'profile' => '2.6',
                            'connection_timeout' => 1,
                            'retry_interval' => 2
                        ]
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
                    ],
                    'custom' => [
                        'metadata_cache_driver' => [
                            'type' => 'redis',
                            'namespace' => null
                        ],
                        'query_cache_driver' => [
                            'type' => 'redis',
                            'namespace' => null
                        ],
                        'result_cache_driver' => [
                            'type' => 'redis',
                            'namespace' => null
                        ],
                        'class_metadata_factory_name' => get_class($classMetadataFactoryMock),
                        'default_repository_class' => get_class($entityRepositoryMock),
                        'repository_factory' => null
                    ]
                ],
                'proxy_dir' => '%kernel.cache_dir%/Another/Dir',
                'proxy_namespace' => 'Another\Proxies\Namespace',
                'default_entity_manager' => 'custom'
            ],
        ];
        
        $this->assertEquals($defaults, $options);
    }
}


<?php

namespace EnderLab\MarvinManagerBundle;

use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\DependencyInjection\Loader\Configurator\ContainerConfigurator;
use Symfony\Component\HttpKernel\Bundle\AbstractBundle;

class MarvinManagerBundle extends AbstractBundle
{
    public function loadExtension(array $config, ContainerConfigurator $container, ContainerBuilder $builder): void
    {
        $container->import('../config/services.yaml');
    }

    public function prependExtension(ContainerConfigurator $container, ContainerBuilder $builder): void
    {
        $env = $builder->getParameterBag()->get('kernel.environment');
        $isTestEnv = $env === 'test';

        if ($isTestEnv) {
            $this->initMessengerTest($builder);
        } else {
            $this->initMessenger($builder);
        }

        $builder->prependExtensionConfig('framework', [
            'messenger' => [
                'routing' => [
                    'EnderLab\\MarvinManagerBundle\\Messenger\\ManagerResponseCommand' => 'manager.to.marvin',
                ]
            ]
        ]);
    }

    private function initMessenger(ContainerBuilder $builder): void {
        $builder->prependExtensionConfig('framework', [
            'messenger' => [
                'transports' => [
                    'marvin.to.manager' => [
                        'dsn' => '%env(MESSENGER_TRANSPORT_DSN)%',
                        'options' => [
                            'auto_setup' => false,
                            'table_name' => 'messenger_marvin_to_manager',
                            'queue_name' => 'marvin.to.manager',
                            'use_notify' => true,
                        ],
                        'retry_strategy' => [
                            'max_retries' => 3,
                            'delay' => 500,
                        ]
                    ],
                    'manager.to.marvin' => [
                        'dsn' => '%env(MESSENGER_TRANSPORT_DSN)%',
                        'options' => [
                            'auto_setup' => false,
                            'table_name' => 'messenger_manager_to_marvin',
                            'queue_name' => 'manager.to.marvin',
                            'use_notify' => true,
                        ],
                        'retry_strategy' => [
                            'max_retries' => 3,
                            'delay' => 500,
                        ]
                    ],
                ],
            ]
        ]);
    }

    private function initMessengerTest(ContainerBuilder $builder): void {
        $builder->prependExtensionConfig('framework', [
            'messenger' => [
                'transports' => [
                    'marvin.to.manager' => 'test://',
                    'manager.to.marvin' => 'test://',
                ],
            ]
        ]);
    }
}

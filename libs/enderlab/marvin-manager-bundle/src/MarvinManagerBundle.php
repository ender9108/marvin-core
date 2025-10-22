<?php

namespace EnderLab\MarvinManagerBundle;

use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\DependencyInjection\Loader\Configurator\ContainerConfigurator;
use Symfony\Component\HttpKernel\Bundle\AbstractBundle;

class MarvinManagerBundle extends AbstractBundle
{
    public function prependExtension(ContainerConfigurator $container, ContainerBuilder $builder): void
    {
        $isManager = str_contains($builder->getParameter('kernel.project_dir'), 'marvin-manager');

        if ($builder->hasParameter('env(MESSENGER_TRANSPORT_DSN)')) {
            $builder->prependExtensionConfig('framework', [
                'messenger' => [
                    'transports' => [
                        'marvin.to.manager' => [
                            'dsn' => '%env(MESSENGER_TRANSPORT_DSN)%',
                            'options' => [
                                'auto_setup' => $isManager,
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
                                'auto_setup' => $isManager,
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
                    'routing' => [
                        'EnderLab\\MarvinManagerBundle\\System\\Infrastructure\\Framework\\Symfony\\Messenger\\ManagerRequestCommand' => 'marvin.to.manager',
                        'EnderLab\\MarvinManagerBundle\\System\\Infrastructure\\Framework\\Symfony\\Messenger\\ManagerResponseCommand' => 'manager.to.marvin',
                    ]
                ]
            ]);
        }
    }
}

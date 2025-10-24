<?php

namespace EnderLab\DddCqrsBundle;

use EnderLab\DddCqrsBundle\Application\Command\CommandHandlerInterface;
use EnderLab\DddCqrsBundle\Application\Command\SyncCommandHandlerInterface;
use EnderLab\DddCqrsBundle\Application\Event\DomainEventHandlerInterface;
use EnderLab\DddCqrsBundle\Application\Query\QueryHandlerInterface;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\DependencyInjection\Loader\Configurator\ContainerConfigurator;
use Symfony\Component\HttpKernel\Bundle\AbstractBundle;

class DddCqrsBundle extends AbstractBundle
{

    public function loadExtension(array $config, ContainerConfigurator $container, ContainerBuilder $builder): void
    {
        $container->import('../config/services.yaml');

        $services = $container->services();
        /*$services
            ->instanceof(CommandHandlerInterface::class)
            ->tag('messenger.message_handler', ['bus' => 'commands'])
        ;

        $services
            ->instanceof(SyncCommandHandlerInterface::class)
            ->tag('messenger.message_handler', ['bus' => 'sync.commands'])
        ;

        $services
            ->instanceof(QueryHandlerInterface::class)
            ->tag('messenger.message_handler', ['bus' => 'queries'])
        ;

        $services
            ->instanceof(DomainEventHandlerInterface::class)
            ->tag('messenger.message_handler', ['bus' => 'domain.events'])
        ;*/
    }

    public function prependExtension(ContainerConfigurator $container, ContainerBuilder $builder): void
    {
        $autoSetup =
            $builder->hasParameter('is_marvin_core') &&
            true === $builder->getParameter('is_marvin_core')
        ;

        $builder->prependExtensionConfig('framework', [
            'messenger' => [
                'transports' => [
                    'domain.events' => [
                        'dsn' => '%env(MESSENGER_TRANSPORT_DSN)%',
                        'options' => [
                            'auto_setup' => $autoSetup,
                            'table_name' => 'messenger_domain_events',
                            'queue_name' => 'domain.events',
                            'use_notify' => true,
                        ],
                        'retry_strategy' => [
                            'max_retries' => 3,
                            'delay' => 500,
                        ]
                    ],
                    'commands' => [
                        'dsn' => '%env(MESSENGER_TRANSPORT_DSN)%',
                        'options' => [
                            'auto_setup' => $autoSetup,
                            'table_name' => 'messenger_domain_commands',
                            'queue_name' => 'command',
                            'use_notify' => true,
                        ],
                        'retry_strategy' => [
                            'max_retries' => 3,
                            'delay' => 500,
                        ]
                    ],
                    'sync.commands' => 'sync://',
                    'queries' => 'sync://',
                ],
                'default_bus' => 'commands',
                'buses' => [
                    'commands' => [
                        'middleware' => [
                            'messenger.middleware.doctrine_transaction',
                        ]
                    ],
                    'sync.commands' => [
                        'middleware' => [
                            'messenger.middleware.doctrine_transaction',
                        ]
                    ],
                    'queries' => [],
                    'domain.events' => [
                        'default_middleware' => [
                            'enabled' => true,
                            'allow_no_handlers' => true
                        ],
                        'middleware' => [
                            'messenger.middleware.doctrine_transaction',
                        ]
                    ],
                ],
                'routing' => [
                    'EnderLab\\DddCqrsBundle\\Application\\Query\\QueryInterface' => 'queries',
                    'EnderLab\\DddCqrsBundle\\Application\\Command\\CommandInterface' => 'commands',
                    'EnderLab\\DddCqrsBundle\\Application\\Command\\SyncCommandInterface' => 'sync.commands',
                    'EnderLab\\DddCqrsBundle\\Domain\\Event\\DomainEventInterface' => 'domain.events',
                ]
            ]
        ]);
    }
}

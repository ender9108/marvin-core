<?php

namespace EnderLab\DddCqrsBundle;

use EnderLab\DddCqrsBundle\Application\Command\CommandHandlerInterface;
use EnderLab\DddCqrsBundle\Application\Command\SyncCommandHandlerInterface;
use EnderLab\DddCqrsBundle\Application\Event\DomainEventHandlerInterface;
use EnderLab\DddCqrsBundle\Application\Query\QueryHandlerInterface;
use EnderLab\DddCqrsBundle\Domain\Event\DomainEventInterface;
use EnderLab\DddCqrsBundle\Infrastructure\Framework\Symfony\DependencyInjection\DomainEventMessengerCompilerPass;
use Symfony\Component\Config\Definition\Configurator\DefinitionConfigurator;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\DependencyInjection\Loader\Configurator\ContainerConfigurator;
use Symfony\Component\HttpKernel\Bundle\AbstractBundle;

class DddCqrsBundle extends AbstractBundle
{
    public const string DEFAULT_EXCHANGE_NAME = 'domain.event.exchange';
    public const string DEFAULT_QUEUE_PREFIX = 'domain.event.';

    public function configure(DefinitionConfigurator $definition): void
    {
        $definition
            ->rootNode()
                ->children()
                    ->stringNode('exchange_name')
                    ->defaultValue(self::DEFAULT_EXCHANGE_NAME)
                    ->end()
                ->end()
                ->children()
                    ->stringNode('queue_prefix')
                    ->defaultValue(self::DEFAULT_QUEUE_PREFIX)
                    ->end()
                ->end()
            ->end()
        ;
    }

    public function build(ContainerBuilder $container): void
    {
        parent::build($container);
        $container->addCompilerPass(new DomainEventMessengerCompilerPass());
    }

    public function loadExtension(array $config, ContainerConfigurator $container, ContainerBuilder $builder): void
    {
        $container->import('../config/services.yaml');

        $services = $container->services();
        $services
            ->instanceof(CommandHandlerInterface::class)
            ->tag('messenger.message_handler', ['bus' => 'command'])
        ;

        $services
            ->instanceof(SyncCommandHandlerInterface::class)
            ->tag('messenger.message_handler', ['bus' => 'sync.command'])
        ;

        $services
            ->instanceof(QueryHandlerInterface::class)
            ->tag('messenger.message_handler', ['bus' => 'query'])
        ;

        $services
            ->instanceof(DomainEventHandlerInterface::class)
            ->tag('messenger.message_handler', ['bus' => 'domain.event'])
        ;

        $builder->setParameter('ddd_cqrs.exchange_name', $config['exchange_name']);
        $builder->setParameter('ddd_cqrs.queue_prefix', $config['queue_prefix']);
    }

    public function prependExtension(ContainerConfigurator $container, ContainerBuilder $builder): void
    {
        $isCore = str_contains($builder->getParameter('kernel.project_dir'), 'marvin-core');

        if ($isCore) {
            $builder->prependExtensionConfig('framework', [
                'messenger' => [
                    'transports' => [
                        'domain.events' => [
                            'dsn' => '%env(MESSENGER_TRANSPORT_DSN)%',
                            'options' => [
                                'table_name' => 'messenger_domain_events',
                                'queue_name' => 'domain.events',
                                'use_notify' => true,
                            ],
                            'retry_strategy' => [
                                'max_retries' => 3,
                                'delay' => 500,
                            ]
                        ],
                        'command' => [
                            'dsn' => '%env(MESSENGER_TRANSPORT_DSN)%',
                            'options' => [
                                'table_name' => 'messenger_domain_commands',
                                'queue_name' => 'command',
                                'use_notify' => true,
                            ],
                            'retry_strategy' => [
                                'max_retries' => 3,
                                'delay' => 500,
                            ]
                        ],
                        'sync.command' => 'sync://',
                        'query' => 'sync://',
                    ],
                    'default_bus' => 'command',
                    'buses' => [
                        'command' => [
                            'middleware' => [
                                'messenger.middleware.doctrine_transaction',
                            ]
                        ],
                        'sync.command' => [
                            'middleware' => [
                                'messenger.middleware.doctrine_transaction',
                            ]
                        ],
                        'query' => [],
                        'domain.event' => [
                            'default_middleware' => 'allow_no_handlers',
                        ],
                    ],
                    'routing' => [
                        'EnderLab\\DddCqrsBundle\\Application\\Query\\QueryInterface' => 'query',
                        'EnderLab\\DddCqrsBundle\\Application\\Command\\CommandInterface' => 'command',
                        'EnderLab\\DddCqrsBundle\\Application\\Command\\SyncCommandInterface' => 'sync.command',
                        'EnderLab\\DddCqrsBundle\\Domain\\Event\\DomainEventInterface' => 'domain.events',
                    ]
                ]
            ]);
        }
    }
}

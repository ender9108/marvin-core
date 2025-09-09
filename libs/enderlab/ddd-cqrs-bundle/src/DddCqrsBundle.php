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
            ->tag('messenger.message_handler', ['bus' => 'command.bus'])
        ;

        $services
            ->instanceof(SyncCommandHandlerInterface::class)
            ->tag('messenger.message_handler', ['bus' => 'sync.command.bus'])
        ;

        $services
            ->instanceof(QueryHandlerInterface::class)
            ->tag('messenger.message_handler', ['bus' => 'query.bus'])
        ;

        $services
            ->instanceof(DomainEventHandlerInterface::class)
            ->tag('messenger.message_handler', ['bus' => 'domain.event.bus'])
        ;

        $builder->setParameter('ddd_cqrs.exchange_name', $config['exchange_name']);
        $builder->setParameter('ddd_cqrs.queue_prefix', $config['queue_prefix']);

        $builder
            ->registerForAutoconfiguration(DomainEventInterface::class)
            ->addTag('enderlab.domain_event_routing_keys')
        ;

        $builder
            ->registerForAutoconfiguration(DomainEventHandlerInterface::class)
            ->addTag('enderlab.domain_event_routing_key_handlers')
        ;
    }

    public function prependExtension(ContainerConfigurator $container, ContainerBuilder $builder): void
    {
        $builder->prependExtensionConfig('framework', [
            'messenger' => [
                'transports' => [
                    'domain.event' => [
                        'dsn' => '%env(MESSENGER_TRANSPORT_DSN)%',
                        'options' => [
                            'exchange' => [
                                'name' => self::DEFAULT_EXCHANGE_NAME,
                                'type' => 'topic',
                            ],
                            'queues' => []
                        ],
                        'retry_strategy' => [
                            'delay' => 500,
                        ]
                    ],
                    'query.messages' => 'sync://',
                    'command.messages' => '%env(MESSENGER_TRANSPORT_DSN)%',
                    'sync.command.messages' => 'sync://'
                ],
                'default_bus' => 'command.bus',
                'buses' => [
                    'command.bus' => [
                        'middleware' => [
                            'messenger.middleware.doctrine_transaction',
                        ]
                    ],
                    'sync.command.bus' => [
                        'middleware' => [
                            'messenger.middleware.doctrine_transaction',
                        ]
                    ],
                    'query.bus' => [],
                    'domain.event.bus' => [
                        'default_middleware' => 'allow_no_handlers',
                        'middleware' => [
                            'EnderLab\\DddCqrsBundle\\Infrastructure\\Framework\\Symfony\\Messenger\\Middleware\\DomainEventRoutingMiddleware',
                        ]
                    ]
                ],
                'routing' => [
                    'EnderLab\\DddCqrsBundle\\Application\\Query\\QueryInterface' => 'query.messages',
                    'EnderLab\\DddCqrsBundle\\Application\\Command\\CommandInterface' => 'command.messages',
                    'EnderLab\\DddCqrsBundle\\Application\\Command\\SyncCommandInterface' => 'sync.command.messages',
                    'EnderLab\\DddCqrsBundle\\Domain\\Event\\DomainEventInterface' => 'domain.event',
                ]
            ]
        ]);
    }
}

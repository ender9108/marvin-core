<?php

namespace EnderLab\DddCqrsBundle;

use EnderLab\DddCqrsBundle\Application\Command\Attribute\AsCommandHandler;
use EnderLab\DddCqrsBundle\Application\Command\Attribute\AsSyncCommandHandler;
use EnderLab\DddCqrsBundle\Application\Query\Attribute\AsQueryHandler;
use EnderLab\DddCqrsBundle\Domain\Event\Attribute\AsDomainEvent;
use EnderLab\DddCqrsBundle\Domain\Event\Attribute\AsDomainEventHandler;
use EnderLab\DddCqrsBundle\Infrastructure\Symfony\DependencyInjection\DomainEventMessengerCompilerPass;
use ReflectionClass;
use Symfony\Component\Config\Definition\Configurator\DefinitionConfigurator;
use Symfony\Component\DependencyInjection\ChildDefinition;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\DependencyInjection\Loader\Configurator\ContainerConfigurator;
use Symfony\Component\HttpKernel\Bundle\AbstractBundle;

class DddCqrsBundle extends AbstractBundle
{
    public const string DEFAULT_EXCHANGE_NAME = 'domain.event.exchange';

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
                    ->stringNode('queue_name_prefix')
                    ->defaultValue('domain.event.')
                    ->end()
                ->end()
                    ->children()
                    ->stringNode('routing_key_pattern')
                    ->defaultValue('$.*.*.*')
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

        $builder->setParameter('ddd_cqrs.exchange_name', $config['exchange_name']);
        $builder->setParameter('ddd_cqrs.queue_name_prefix', $config['queue_name_prefix']);
        $builder->setParameter('ddd_cqrs.routing_key_pattern', $config['routing_key_pattern']);

        $builder->registerAttributeForAutoconfiguration(AsCommandHandler::class, static function (
            ChildDefinition $definition,
        ): void {
            $definition
                ->addTag('messenger.message_handler', ['bus' => 'command.bus'])
                ->setLazy(true)
            ;
        });

        $builder->registerAttributeForAutoconfiguration(AsSyncCommandHandler::class, static function (
            ChildDefinition $definition,
        ): void {
            $definition
                ->addTag('messenger.message_handler', ['bus' => 'sync.command.bus'])
                ->setLazy(true)
            ;
        });

        $builder->registerAttributeForAutoconfiguration(AsQueryHandler::class, static function (
            ChildDefinition $definition,
        ): void {
            $definition
                ->addTag('messenger.message_handler', ['bus' => 'query.bus'])
                ->setLazy(true)
            ;
        });

        $builder->registerAttributeForAutoconfiguration(AsDomainEventHandler::class, static function (
            ChildDefinition $definition,
            AsDomainEventHandler $attribute,
            ReflectionClass $reflector
        ): void {
            $definition
                ->addTag('messenger.message_handler', ['bus' => 'domain_event.bus'])
                ->addTag('messenger.domain_event_listener')
                ->setLazy(true)
            ;

            $definition->addTag('enderlab.domain_event_routing_key_handlers', [
                'routingKeys' => $attribute->routingKeys,
            ]);
        });

        $builder->registerAttributeForAutoconfiguration(AsDomainEvent::class, function (
            ChildDefinition $definition,
            AsDomainEvent $attribute
        ): void {
                $definition->addTag('enderlab.domain_event_routing_keys', [
                    'routingKey' => $attribute->routingKey,
                ]);
            }
        );
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
                            'EnderLab\\DddCqrsBundle\\Infrastructure\\Messenger\\Middleware\\DomainExceptionMiddleware',
                            'messenger.middleware.doctrine_transaction',
                            'validation',
                        ]
                    ],
                    'sync.command.bus' => [
                        'middleware' => [
                            'EnderLab\\DddCqrsBundle\\Infrastructure\\Messenger\\Middleware\\DomainExceptionMiddleware',
                            'messenger.middleware.doctrine_transaction',
                            'validation',
                        ]
                    ],
                    'query.bus' => [],
                    'domain_event.bus' => [
                        'middleware' => [
                            'EnderLab\\DddCqrsBundle\\Infrastructure\\Messenger\\Middleware\\DomainEventRoutingMiddleware',
                            'validation',
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

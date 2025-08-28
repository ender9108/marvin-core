<?php

namespace EnderLab\MarvinManagerBundle;

use EnderLab\MarvinManagerBundle\Messenger\Attribute\AsMessageType;
use ReflectionClass;
use Symfony\Component\DependencyInjection\ChildDefinition;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\DependencyInjection\Loader\Configurator\ContainerConfigurator;
use Symfony\Component\HttpKernel\Bundle\AbstractBundle;

class MarvinManagerBundle extends AbstractBundle
{
    public const string APP_MARVIN_MANAGER = 'marvin-manager';
    public const string APP_MARVIN_CORE = 'marvin-core';

    public function loadExtension(array $config, ContainerConfigurator $container, ContainerBuilder $builder): void
    {
        $container->import('../config/services.yaml');

        $builder->registerAttributeForAutoconfiguration(AsMessageType::class, static function (
            ChildDefinition $definition,
            AsMessageType $attribute
        ): void {
            $definition->addTag('marvin.message.handler', ['binding' => $attribute->binding]);
        });
    }

    public function prependExtension(ContainerConfigurator $container, ContainerBuilder $builder): void
    {
        $isAutoSetup = $builder->getParameter('app_name') === self::APP_MARVIN_MANAGER;

        $builder->prependExtensionConfig('framework', [
            'messenger' => [
                'transports' => [
                    'marvin.to.manager' => [
                        'dsn' => '%env(MESSENGER_TRANSPORT_DSN)%',
                        'serializer' => 'EnderLab\\MarvinManagerBundle\\Messenger\\Serializer\\ManagerSerializer',
                        'options' => [
                            'auto_setup' => $isAutoSetup,
                            'exchange' => [
                                'name' => 'marvin.to.manager',
                                'type' => 'direct',
                                'default_publish_routing_key' => 'marvin.to.manager.request'
                            ],
                            'queues' => [
                                'manager.to.marvin.response' => [
                                    'binding_keys' => 'manager.to.marvin.response'
                                ]
                            ]
                        ],
                        'retry_strategy' => [
                            'delay' => 500,
                        ]
                    ],
                ],
                'routing' => [
                    'EnderLab\\MarvinManagerBundle\\Messenger\\ManagerRequestCommand' => 'marvin.to.manager'
                ]
            ]
        ]);
    }
}

<?php

namespace EnderLab\Zigbee2mqttBundle;

use App\Domotic\Infrastructure\Attribute\AsMqttListener;
use Symfony\Component\DependencyInjection\ChildDefinition;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\DependencyInjection\Loader\Configurator\ContainerConfigurator;
use Symfony\Component\HttpKernel\Bundle\AbstractBundle;

class Zigbee2mqttBundle extends AbstractBundle
{
    public function loadExtension(array $config, ContainerConfigurator $container, ContainerBuilder $builder): void
    {
        $container->import('../config/services.yaml');
    }

    public function prependExtension(ContainerConfigurator $container, ContainerBuilder $builder): void
    {
        $builder->prependExtensionConfig('framework', [
            'messenger' => [
                'transports' => [
                    'zigbee.commands' => [
                        'dsn' => '%env(MESSENGER_TRANSPORT_DSN)%',
                        'options' => [
                            'queues' => [
                                'zigbee.commands'
                            ]
                        ],
                        'retry_strategy' => [
                            'delay' => 500,
                        ]
                    ]
                ]
            ]
        ]);
    }
}

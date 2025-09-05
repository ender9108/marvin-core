<?php

namespace EnderLab\MarvinManagerBundle;

use EnderLab\MarvinManagerBundle\System\Domain\Repository\DockerCustomCommandRepositoryInterface;
use EnderLab\MarvinManagerBundle\System\Domain\Repository\DockerRepositoryInterface;
use EnderLab\MarvinManagerBundle\System\Infrastructure\Doctrine\Repository\DoctrineDockerCustomCommandRepository;
use EnderLab\MarvinManagerBundle\System\Infrastructure\Doctrine\Repository\DoctrineDockerRepository;
use EnderLab\MarvinManagerBundle\System\Infrastructure\Symfony\Messenger\Attribute\AsMessageType;
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

        $services = $container->services();

        $services->set(DockerRepositoryInterface::class)->class(DoctrineDockerRepository::class);
        $services->set(DockerCustomCommandRepositoryInterface::class)->class(DoctrineDockerCustomCommandRepository::class);

        $builder->registerAttributeForAutoconfiguration(AsMessageType::class, static function (
            ChildDefinition $definition,
            AsMessageType $attribute
        ): void {
            $definition->addTag('marvin.message.handler', ['binding' => $attribute->binding]);
        });
    }

    public function prependExtension(ContainerConfigurator $container, ContainerBuilder $builder): void
    {
        $isAutoSetup = false;

        if ($builder->hasParameter('shared.app_name') === false) {
            $isAutoSetup = true;
        }

        $builder->prependExtensionConfig('framework', [
            'messenger' => [
                'transports' => [
                    'marvin.to.manager' => [
                        'dsn' => '%env(MESSENGER_TRANSPORT_DSN)%',
                        'serializer' => 'EnderLab\\MarvinManagerBundle\\System\\Infrastructure\\Symfony\\Messenger\\Serializer\\ManagerSerializer',
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
                    'EnderLab\\MarvinManagerBundle\\System\\Infrastructure\\Symfony\\Messenger\\ManagerRequestCommand' => 'marvin.to.manager'
                ]
            ]
        ]);
        /*$builder->prependExtensionConfig('doctrine', [
            'orm' => [
                'mappings' => [
                    'marvin_manager_mapping' => [
                        'is_bundle' => false,
                        'type' => 'attribute',
                        'dir' => __DIR__.'/System/Domain',
                        'prefix' => 'EnderLab\MarvinManagerBundle\System\Domain',
                        'alias' => 'MarvinManagerBundle',
                    ],
                ]
            ]
        ]);
        $builder->prependExtensionConfig('api_platform', [
            'mapping' => [
                'paths' => [
                    __DIR__ . '/System/Infrastructure/ApiPlatform/Resource'
                ]
            ]
        ]);*/
    }
}

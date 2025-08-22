<?php

namespace EnderLab\LockableBundle;

use EnderLab\LockableBundle\Attribute\AsLockableMessage;
use ReflectionClass;
use Symfony\Component\DependencyInjection\ChildDefinition;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\DependencyInjection\Loader\Configurator\ContainerConfigurator;
use Symfony\Component\HttpKernel\Bundle\AbstractBundle;

class LockableBundle extends AbstractBundle
{
    public function loadExtension(array $config, ContainerConfigurator $container, ContainerBuilder $builder): void
    {
        $container->import('../config/services.yaml');

        $builder->registerAttributeForAutoconfiguration(AsLockableMessage::class, static function (
            ChildDefinition $definition
        ): void {
            $definition
                ->addTag('app.lockable_message')
                ->setLazy(true)
            ;
        });
    }
}

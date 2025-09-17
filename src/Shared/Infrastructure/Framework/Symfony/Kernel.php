<?php

namespace Marvin\Shared\Infrastructure\Framework\Symfony;

use Symfony\Bundle\FrameworkBundle\Kernel\MicroKernelTrait;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\DependencyInjection\Loader\Configurator\ContainerConfigurator;
use Symfony\Component\HttpKernel\Kernel as BaseKernel;

class Kernel extends BaseKernel
{
    use MicroKernelTrait;

    protected function configureContainer(ContainerConfigurator $container): void
    {
        $container->import(sprintf('%s/config/{packages}/*.yaml', $this->getProjectDir()));
        $container->import(sprintf('%s/config/{packages}/%s/*.yaml', $this->getProjectDir(), $this->environment));
        $container->import(sprintf('%s/config/{services}/*.php', $this->getProjectDir()));
        $container->import(sprintf('%s/config/{services}/%s/*.php', $this->getProjectDir(), $this->environment));
    }

    protected function build(ContainerBuilder $container): void
    {
    }
}

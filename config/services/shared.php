<?php

declare(strict_types=1);

use Symfony\Component\DependencyInjection\Loader\Configurator\ContainerConfigurator;

return static function (ContainerConfigurator $containerConfigurator): void {
    $services = $containerConfigurator->services();
    $parameters = $containerConfigurator->parameters();

    $parameters
        ->set('shared.cache_timeout', 60)
        ->set('shared.project_dir', '%kernel.project_dir%')
        ->set('shared.app_name', '%env(APP_NAME)%')
        ->set('shared.cache_timeout', 3600)
    ;

    if ('dev' === $containerConfigurator->env()) {
        $parameters->set('shared.cache_timeout', 1);
    }

    $services
        ->defaults()
        ->autowire()
        ->autoconfigure()
    ;

    $services
        ->load('App\\Shared\\', dirname(__DIR__, 2).'/src/Shared')
        ->exclude([dirname(__DIR__, 2).'/src/Shared/Infrastructure/Symfony/Kernel.php'])
    ;
    $services->load('App\\Shared\\Infrastructure\\DataFixtures\\', dirname(__DIR__, 2).'/src/Shared/Infrastructure/DataFixtures/');

    // repositories
};

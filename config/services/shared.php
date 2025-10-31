<?php

use Symfony\Component\DependencyInjection\Loader\Configurator\ContainerConfigurator;

return static function (ContainerConfigurator $containerConfigurator): void {
    $services = $containerConfigurator->services();
    $parameters = $containerConfigurator->parameters();

    $parameters
        ->set('shared.cache_timeout', 3600)
        ->set('shared.project_dir', '%kernel.project_dir%')
        ->set('shared.app_name', '%env(APP_NAME)%')
        ->set('is_marvin_core', true)
    ;

    if (in_array($containerConfigurator->env(), ['dev', 'test'])) {
        $parameters->set('shared.cache_timeout', 1);
    }

    $services
        ->defaults()
        ->autowire()
        ->autoconfigure()
    ;

    $services
        ->load('Marvin\\Shared\\', dirname(__DIR__, 2).'/src/Shared')
        ->exclude([
            dirname(__DIR__, 2).'/src/Shared/Infrastructure/Framework/Symfony/Kernel.php',
            dirname(__DIR__, 2).'/src/Shared/Domain/Model/*',
            dirname(__DIR__, 2).'/src/Shared/Domain/ValueObject/*',
        ])
    ;


};

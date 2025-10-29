<?php
use Symfony\Component\DependencyInjection\Loader\Configurator\ContainerConfigurator;

return static function (ContainerConfigurator $containerConfigurator): void {
    $services = $containerConfigurator->services();

    $services
        ->defaults()
        ->autowire()
        ->autoconfigure()
    ;

    $services
        ->load('Marvin\\PluginManager\\Infrastructure\\Framework\\Symfony\\DataFixtures\\', dirname(__DIR__, 2) . '/src/PluginManager/Infrastructure/Framework/Symfony/DataFixtures/')
        ->load('Marvin\\PluginManager\\', dirname(__DIR__, 2).'/src/PluginManager')
        ->exclude([
            dirname(__DIR__, 2).'/src/PluginManager/Domain/Model/*',
            dirname(__DIR__, 2).'/src/PluginManager/Domain/ValueObject/*',
        ])
    ;
};

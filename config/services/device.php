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
        ->load('Marvin\\Device\\Infrastructure\\Framework\\Symfony\\DataFixtures\\', dirname(__DIR__, 2) . '/src/Device/Infrastructure/Framework/Symfony/DataFixtures/')
        ->load('Marvin\\Device\\', dirname(__DIR__, 2).'/src/Device')
        ->exclude([
            dirname(__DIR__, 2).'/src/Device/Domain/Model/*',
            dirname(__DIR__, 2).'/src/Device/Domain/ValueObject/*',
        ])
    ;
};

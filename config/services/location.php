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
        ->load('Marvin\\Location\\Infrastructure\\Framework\\Symfony\\DataFixtures\\', dirname(__DIR__, 2) . '/src/Location/Infrastructure/Framework/Symfony/DataFixtures/')
        ->load('Marvin\\Location\\', dirname(__DIR__, 2).'/src/Location')
        ->exclude([
            dirname(__DIR__, 2).'/src/Location/Domain/Model/*',
            dirname(__DIR__, 2).'/src/Location/Domain/ValueObject/*',
        ])
    ;
};

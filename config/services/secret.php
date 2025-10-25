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
        ->load('Marvin\\Secret\\Infrastructure\\Framework\\Symfony\\DataFixtures\\', dirname(__DIR__, 2) . '/src/Secret/Infrastructure/Framework/Symfony/DataFixtures/')
        ->load('Marvin\\Secret\\', dirname(__DIR__, 2).'/src/Secret')
        ->exclude([
            dirname(__DIR__, 2).'/src/Secret/Domain/Model/*',
            dirname(__DIR__, 2).'/src/Secret/Domain/ValueObject/*',
        ])
    ;
};

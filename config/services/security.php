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
        ->load('Marvin\\Security\\Infrastructure\\Framework\\Symfony\\DataFixtures\\', dirname(__DIR__, 2) . '/src/Security/Infrastructure/Framework/Symfony/DataFixtures/')
        ->load('Marvin\\Security\\', dirname(__DIR__, 2).'/src/Security')
        ->exclude([
            dirname(__DIR__, 2).'/src/Security/Domain/Model/*',
            dirname(__DIR__, 2).'/src/Security/Domain/ValueObject/*',
            dirname(__DIR__, 2).'/src/Security/Domain/Event/*',
        ])
    ;
};

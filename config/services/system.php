<?php

declare(strict_types=1);

use Symfony\Component\DependencyInjection\Loader\Configurator\ContainerConfigurator;

return static function (ContainerConfigurator $containerConfigurator): void {
    $services = $containerConfigurator->services();

    $services
        ->defaults()
        ->autowire()
        ->autoconfigure()
    ;

    $services
        ->load('Marvin\\System\\Infrastructure\\Framework\\Symfony\\DataFixtures\\', dirname(__DIR__, 2).'/src/System/Infrastructure/Framework/Symfony/DataFixtures/')
        ->load('Marvin\\System\\', dirname(__DIR__, 2).'/src/System')
        ->exclude([
            dirname(__DIR__, 2).'/src/System/Domain/Model/*',
            dirname(__DIR__, 2).'/src/System/Domain/ValueObject/*',
        ])
    ;
};

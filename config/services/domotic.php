<?php
use Symfony\Component\DependencyInjection\Loader\Configurator\ContainerConfigurator;

return static function (ContainerConfigurator $containerConfigurator): void {
    $services = $containerConfigurator->services();

    $services
        ->defaults()
        ->autowire()
        ->autoconfigure()
    ;

    $services->load('Marvin\\Domotic\\Infrastructure\\Framework\\Symfony\\DataFixtures\\', dirname(__DIR__, 2) . '/src/Domotic/Infrastructure/Framework/Symfony/DataFixtures/');
    $services->load('Marvin\\Domotic\\', dirname(__DIR__, 2).'/src/Domotic');
};
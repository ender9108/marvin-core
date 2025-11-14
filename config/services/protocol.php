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

    // Auto-load all Protocol services
    $services
        ->load('Marvin\\Protocol\\', dirname(__DIR__, 2) . '/src/Protocol')
        ->exclude([
            dirname(__DIR__, 2) . '/src/Protocol/Domain/Model/*',
            dirname(__DIR__, 2) . '/src/Protocol/Domain/ValueObject/*',
            dirname(__DIR__, 2) . '/src/Protocol/Domain/Event/*',
        ])
    ;

    // Repository Interface
    $services
        ->alias(
            'Marvin\\Protocol\\Domain\\Repository\\ProtocolRepositoryInterface',
            'Marvin\\Protocol\\Infrastructure\\Persistence\\Doctrine\\ORM\\ProtocolOrmRepository'
        )
    ;

    // Tag all Protocol Adapters
    $services
        ->instanceof('Marvin\\Protocol\\Domain\\Model\\ProtocolAdapterInterface')
        ->tag('protocol.adapter')
    ;
};

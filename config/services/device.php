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
        ->load('Marvin\\Device\\', dirname(__DIR__, 2).'/src/Device')
        ->exclude([
            dirname(__DIR__, 2).'/src/Device/Domain/Model/*',
            dirname(__DIR__, 2).'/src/Device/Domain/ValueObject/*',
            dirname(__DIR__, 2).'/src/Device/Domain/Event/*',
        ])
    ;

    // ACL Service Interfaces (Device → Protocol)
    /*$services
        ->alias(
            'Marvin\\Device\\Application\\Service\\Acl\\ProtocolQueryServiceInterface',
            'Marvin\\Device\\Infrastructure\\Framework\\Symfony\\Service\\Acl\\ProtocolQueryService'
        )
        ->alias(
            'Marvin\\Device\\Application\\Service\\Acl\\ProtocolCapabilityServiceInterface',
            'Marvin\\Device\\Infrastructure\\Framework\\Symfony\\Service\\Acl\\ProtocolCapabilityService'
        )
    ;*/
    $services
        ->alias(
            'Marvin\\Shared\\Application\\Service\\Acl\\DeviceQueryServiceInterface',
            'Marvin\\Device\\Infrastructure\\Framework\\Symfony\\Service\\Acl\\DeviceQueryService'
        )
    ;

    // Repository Interfaces
    $services
        ->alias(
            'Marvin\\Device\\Domain\\Repository\\DeviceRepositoryInterface',
            'Marvin\\Device\\Infrastructure\\Persistence\\Doctrine\\ORM\\Repository\\DoctrineDeviceRepository'
        )
        ->alias(
            'Marvin\\Device\\Domain\\Repository\\PendingActionRepositoryInterface',
            'Marvin\\Device\\Infrastructure\\Persistence\\Doctrine\\ORM\\Repository\\DoctrinePendingActionRepository'
        )
    ;
};

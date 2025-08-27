<?php

declare(strict_types=1);

use App\Domotic\Domain\Repository\CapabilityActionRepositoryInterface;
use App\Domotic\Domain\Repository\CapabilityCompositionRepositoryInterface;
use App\Domotic\Domain\Repository\CapabilityRepositoryInterface;
use App\Domotic\Domain\Repository\CapabilityStateRepositoryInterface;
use App\Domotic\Domain\Repository\DeviceRepositoryInterface;
use App\Domotic\Domain\Repository\GroupRepositoryInterface;
use App\Domotic\Domain\Repository\ProtocolRepositoryInterface;
use App\Domotic\Domain\Repository\ProtocolStatusRepositoryInterface;
use App\Domotic\Domain\Repository\ZoneRepositoryInterface;
use App\Domotic\Infrastructure\Doctrine\Repository\DoctrineCapabilityActionRepository;
use App\Domotic\Infrastructure\Doctrine\Repository\DoctrineCapabilityCompositionRepository;
use App\Domotic\Infrastructure\Doctrine\Repository\DoctrineCapabilityRepository;
use App\Domotic\Infrastructure\Doctrine\Repository\DoctrineCapabilityStateRepository;
use App\Domotic\Infrastructure\Doctrine\Repository\DoctrineDeviceRepository;
use App\Domotic\Infrastructure\Doctrine\Repository\DoctrineGroupRepository;
use App\Domotic\Infrastructure\Doctrine\Repository\DoctrineProtocolRepository;
use App\Domotic\Infrastructure\Doctrine\Repository\DoctrineProtocolStatusRepository;
use App\Domotic\Infrastructure\Doctrine\Repository\DoctrineZoneRepository;
use Symfony\Component\DependencyInjection\Loader\Configurator\ContainerConfigurator;

return static function (ContainerConfigurator $containerConfigurator): void {
    $services = $containerConfigurator->services();

    $services
        ->defaults()
        ->autowire()
        ->autoconfigure()
    ;

    $services->load('App\\Domotic\\Infrastructure\\DataFixtures\\', dirname(__DIR__, 2) . '/src/Domotic/Infrastructure/DataFixtures/');
    $services->load('App\\Domotic\\', dirname(__DIR__, 2) . '/src/Domotic');

    // repositories
    $services->set(CapabilityRepositoryInterface::class)->class(DoctrineCapabilityRepository::class);
    $services->set(CapabilityActionRepositoryInterface::class)->class(DoctrineCapabilityActionRepository::class);
    $services->set(CapabilityCompositionRepositoryInterface::class)->class(DoctrineCapabilityCompositionRepository::class);
    $services->set(CapabilityStateRepositoryInterface::class)->class(DoctrineCapabilityStateRepository::class);
    $services->set(DeviceRepositoryInterface::class)->class(DoctrineDeviceRepository::class);
    $services->set(GroupRepositoryInterface::class)->class(DoctrineGroupRepository::class);
    $services->set(ProtocolRepositoryInterface::class)->class(DoctrineProtocolRepository::class);
    $services->set(ProtocolStatusRepositoryInterface::class)->class(DoctrineProtocolStatusRepository::class);
    $services->set(ZoneRepositoryInterface::class)->class(DoctrineZoneRepository::class);
};

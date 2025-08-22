<?php

declare(strict_types=1);

use App\Domotic\Domain\Repository\CapabilityRepositoryInterface;
use App\Domotic\Domain\Repository\ZoneRepositoryInterface;
use App\Domotic\Infrastructure\Doctrine\Repository\DoctrineCapabilityRepository;
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
    $services->set(ZoneRepositoryInterface::class)->class(DoctrineZoneRepository::class);
    $services->set(CapabilityRepositoryInterface::class)->class(DoctrineCapabilityRepository::class);
};

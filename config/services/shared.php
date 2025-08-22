<?php

declare(strict_types=1);

use App\Shared\Domain\Repository\TagRepositoryInterface;
use App\Shared\Infrastructure\Symfony\Repository\DoctrineTagRepository;
use Symfony\Component\DependencyInjection\Loader\Configurator\ContainerConfigurator;

return static function (ContainerConfigurator $containerConfigurator): void {
    $services = $containerConfigurator->services();
    $parameters = $containerConfigurator->parameters();

    $parameters
        ->set('cache_timeout', 60)
        ->set('project_dir', '%kernel.project_dir%')
        ->set('app_name', '%env(APP_NAME)%')
    ;

    $services
        ->defaults()
        ->autowire()
        ->autoconfigure()
    ;

    $services
        ->load('App\\Shared\\', dirname(__DIR__, 2).'/src/Shared')
        ->exclude([dirname(__DIR__, 2).'/src/Shared/Infrastructure/Symfony/Kernel.php'])
    ;
    $services->load('App\\Shared\\Infrastructure\\DataFixtures\\', dirname(__DIR__, 2).'/src/Shared/Infrastructure/DataFixtures/');

    // repositories
    $services->set(TagRepositoryInterface::class)->class(DoctrineTagRepository::class);
};

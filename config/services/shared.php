<?php
use EnderLab\DddCqrsBundle\Application\Command\CommandHandlerInterface;
use EnderLab\DddCqrsBundle\Application\Command\SyncCommandHandlerInterface;
use EnderLab\DddCqrsBundle\Application\Event\DomainEventHandlerInterface;
use EnderLab\DddCqrsBundle\Application\Query\QueryHandlerInterface;
use Symfony\Component\DependencyInjection\Loader\Configurator\ContainerConfigurator;

return static function (ContainerConfigurator $containerConfigurator): void {
    $services = $containerConfigurator->services();
    $parameters = $containerConfigurator->parameters();

    $parameters
        ->set('shared.cache_timeout', 60)
        ->set('shared.project_dir', '%kernel.project_dir%')
        ->set('shared.app_name', '%env(APP_NAME)%')
        ->set('shared.cache_timeout', 3600)
    ;

    if ('dev' === $containerConfigurator->env()) {
        $parameters->set('shared.cache_timeout', 1);
    }

    $services
        ->defaults()
        ->autowire()
        ->autoconfigure()
    ;

    $services
        ->load('Marvin\\Shared\\', dirname(__DIR__, 2).'/src/Shared')
        ->exclude([
            dirname(__DIR__, 2).'/src/Shared/Infrastructure/Framework/Symfony/Kernel.php',
            dirname(__DIR__, 2).'/src/**/Domain/Model/*',
            dirname(__DIR__, 2).'/src/**/Domain/ValueObject/*',
        ])
    ;
    $services->load('Marvin\\Shared\\Infrastructure\\Framework\\Symfony\\DataFixtures\\', dirname(__DIR__, 2).'/src/Shared/Infrastructure/Framework/Symfony/DataFixtures/');

    // repositories
};

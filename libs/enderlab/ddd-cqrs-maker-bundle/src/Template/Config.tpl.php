<?= "<?php\n"; ?>

declare(strict_types=1);

use Symfony\Component\DependencyInjection\Loader\Configurator\ContainerConfigurator;

return static function (ContainerConfigurator $containerConfigurator): void {
    $services = $containerConfigurator->services();

    $services
        ->defaults()
        ->autowire()
        ->autoconfigure()
    ;

    $services->load('App\\<?= $domainName ?>\\Infrastructure\\DataFixtures\\', dirname(__DIR__, 2) . '/src/<?= $domainName ?>/Infrastructure/DataFixtures/');
    $services->load('App\\<?= $domainName ?>\\', dirname(__DIR__, 2) . '/src/<?= $domainName ?>');
};

<?php

use Marvin\Shared\Application\Service\Acl\SecretQueryServiceInterface as SharedSecretQueryServiceInterface;
use Marvin\Secret\Infrastructure\Framework\Symfony\Service\Acl\SecretQueryService;
use Marvin\Shared\Infrastructure\Cache\CacheableSecretQueryService;
use Symfony\Component\DependencyInjection\Loader\Configurator\ContainerConfigurator;
use function Symfony\Component\DependencyInjection\Loader\Configurator\service;

return static function (ContainerConfigurator $containerConfigurator): void {
    $services = $containerConfigurator->services();

    $services
        ->defaults()
        ->autowire()
        ->autoconfigure()
    ;

    $services
        ->load('Marvin\\Secret\\Infrastructure\\Framework\\Symfony\\DataFixtures\\', dirname(__DIR__, 2) . '/src/Secret/Infrastructure/Framework/Symfony/DataFixtures/')
        ->load('Marvin\\Secret\\', dirname(__DIR__, 2).'/src/Secret')
        ->exclude([
            dirname(__DIR__, 2).'/src/Secret/Domain/Model/*',
            dirname(__DIR__, 2).'/src/Secret/Domain/ValueObject/*',
            dirname(__DIR__, 2).'/src/Secret/Domain/Event/*',
        ])
    ;

    // ACL
    $services
        ->alias(
            'Marvin\Shared\Application\Service\Acl\SecretQueryServiceInterface',
            'Marvin\\Secret\\Infrastructure\\Framework\\Symfony\\Service\\Acl\\SecretQueryService'
        )
    ;

    $services
        ->set(SharedSecretQueryServiceInterface::class, CacheableSecretQueryService::class)
        ->arg('$decorated', service(SecretQueryService::class))
        ->arg('$cache', service('cache.app'))
        ->public(true)
    ;
};

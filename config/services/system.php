<?php

declare(strict_types=1);

use App\System\Domain\Repository\UserRepositoryInterface;
use App\System\Domain\Repository\UserStatusRepositoryInterface;
use App\System\Domain\Repository\UserTypeRepositoryInterface;
use App\System\Infrastructure\Doctrine\Repository\DoctrineUserRepository;
use App\System\Infrastructure\Doctrine\Repository\DoctrineUserStatusRepository;
use App\System\Infrastructure\Doctrine\Repository\DoctrineUserTypeRepository;
use App\System\Infrastructure\Symfony\Service\MqttService;
use Symfony\Component\DependencyInjection\Loader\Configurator\ContainerConfigurator;

return static function (ContainerConfigurator $containerConfigurator): void {
    $services = $containerConfigurator->services();
    $parameters = $containerConfigurator->parameters();

    $parameters
        ->set('config_path', '%kernel.project_dir%/config')
        ->set('docker_path', '%kernel.project_dir%/docker')
        ->set('compose_file_path', '%kernel.project_dir%/compose.yaml')
        ->set('plugin_map_path', '%kernel.project_dir%/config/plugins/plugin_map.json')
    ;

    $services
        ->defaults()
        ->autowire()
        ->autoconfigure()
    ;

    $services->set(MqttService::class)
        ->args([
            '%env(string:MQTT_HOST)%',
            '%env(int:MQTT_PORT)%',
            'marvin-core-mqtt',
            '%env(default::null:MQTT_USER)%',
            '%env(default::null:MQTT_PASSWORD)%',
            '%env(default::5:MQTT_PROTOCOL_LEVEL)%',
            '%env(bool:default::false:MQTT_TLS)%',
            '%env(MQTT_CERT_FILE)%)',
            '%env(MQTT_KEY_FILE)%)',
            '%env(MQTT_CA_FILE)%)',
            '%env(bool:default::true:MQTT_VERIFY_PEER)%)',
            '%env(bool:default::false:MQTT_ALLOW_SELF_SIGNED)%)',
        ])
        //->public()
    ;

    $services->load('App\\System\\Infrastructure\\DataFixtures\\', dirname(__DIR__, 2) . '/src/System/Infrastructure/DataFixtures/');
    $services->load('App\\System\\', dirname(__DIR__, 2) . '/src/System');

    // repositories
    $services->set(UserRepositoryInterface::class)->class(DoctrineUserRepository::class);
    $services->set(UserStatusRepositoryInterface::class)->class(DoctrineUserStatusRepository::class);
    $services->set(UserTypeRepositoryInterface::class)->class(DoctrineUserTypeRepository::class);
};

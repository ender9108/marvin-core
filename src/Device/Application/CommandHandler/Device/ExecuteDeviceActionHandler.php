<?php

namespace Marvin\Device\Application\CommandHandler\Device;

use EnderLab\DddCqrsBundle\Application\Command\SyncCommandHandlerInterface;
use Marvin\Device\Application\Command\Device\ExecuteDeviceAction;
use Marvin\Device\Domain\Model\Device;
use Marvin\Device\Domain\Repository\DeviceRepositoryInterface;
use Marvin\Device\Domain\ValueObject\CompositeStrategy;
use Marvin\Device\Domain\ValueObject\DeviceType;
use Psr\Log\LoggerInterface;
use Symfony\Component\Messenger\Attribute\AsMessageHandler;

#[AsMessageHandler]
final readonly class ExecuteDeviceActionHandler implements SyncCommandHandlerInterface
{
    private const int TIMEOUT_PER_DEVICE_MS = 5000; // 5 secondes par device
    private const int FIRST_RESPONSE_WAIT_MS = 100; // 100ms pour first_response

    public function __construct(
        private DeviceRepositoryInterface $deviceRepository,
        //private ProtocolClientInterface $protocolClient,
        private LoggerInterface $logger,
    ) {
    }

    public function __invoke(ExecuteDeviceAction $command): array
    {
        $this->logger->info('Executing device action', [
            'deviceId' => $command->deviceId,
            'capability' => $command->capability,
            'action' => $command->action,
        ]);

        $device = $this->deviceRepository->byId($command->deviceId);

        // Si c'est un device simple, exécuter directement
        if ($device->type !== DeviceType::COMPOSITE) {
            return $this->executeSingleDevice($device, $command);
        }

        // Si c'est un composite, utiliser la stratégie appropriée
        return $this->executeComposite($device, $command);
    }

    private function executeSingleDevice(Device $device, ExecuteDeviceAction $command): array
    {
        $result = $this->protocolClient->executeAction(
            protocolId: $device->protocolId->toString(),
            nativeId: $device->getNativeId(),
            capability: $command->capability,
            action: $command->action,
            parameters: $command->parameters,
            timeout: self::TIMEOUT_PER_DEVICE_MS
        );

        return [
            'success' => $result['success'],
            'strategy' => 'single',
            'results' => [
                [
                    'deviceId' => $device->id->toString(),
                    'deviceName' => $device->label->value,
                    'success' => $result['success'],
                    'response' => $result['response'] ?? null,
                    'error' => $result['error'] ?? null,
                ]
            ],
        ];
    }

    private function executeComposite(Device $composite, ExecuteDeviceAction $command): array
    {
        return match ($composite->compositeStrategy) {
            CompositeStrategy::AGGREGATE => $this->executeAggregate($composite, $command),
            CompositeStrategy::FIRST_RESPONSE => $this->executeFirstResponse($composite, $command),
            CompositeStrategy::BROADCAST => $this->executeBroadcast($composite, $command),
            default => $this->executeSequential($composite, $command), // Fallback
        };
    }

    /**
     * Exécute l'action sur tous les devices et agrège les résultats
     * Attend que tous répondent (avec timeout par device)
     */
    private function executeAggregate(Device $composite, ExecuteDeviceAction $command): array
    {
        $this->logger->debug('Executing aggregate strategy', [
            'compositeId' => $composite->id->toString(),
        ]);

        $childDevices = $this->loadChildDevices($composite);
        $results = [];
        $successCount = 0;

        foreach ($childDevices as $device) {
            try {
                $result = $this->protocolClient->executeAction(
                    protocolId: $device->protocolId->toString(),
                    nativeId: $device->getNativeId(),
                    capabilityName: $command->capabilityName,
                    action: $command->action,
                    parameters: $command->parameters,
                    timeout: self::TIMEOUT_PER_DEVICE_MS
                );

                $results[] = [
                    'deviceId' => $device->id->toString(),
                    'deviceName' => $device->label->value,
                    'success' => $result['success'],
                    'response' => $result['response'] ?? null,
                    'error' => $result['error'] ?? null,
                ];

                if ($result['success']) {
                    $successCount++;
                }
            } catch (\Throwable $e) {
                $results[] = [
                    'deviceId' => $device->id->toString(),
                    'deviceName' => $device->label->value,
                    'success' => false,
                    'response' => null,
                    'error' => $e->getMessage(),
                ];
            }
        }

        return [
            'success' => $successCount === count($childDevices), // Tous ont réussi ?
            'strategy' => 'aggregate',
            'successCount' => $successCount,
            'totalCount' => count($childDevices),
            'results' => $results,
        ];
    }

    /**
     * Exécute l'action sur tous les devices et retourne dès la première réponse
     * Attend 100ms minimum pour éviter de toujours prendre le même device
     */
    private function executeFirstResponse(Device $composite, ExecuteDeviceAction $command): array
    {
        $this->logger->debug('Executing first_response strategy', [
            'compositeId' => $composite->id->toString(),
        ]);

        $childDevices = $this->loadChildDevices($composite);
        $results = [];
        $firstResponse = null;
        $startTime = microtime(true);

        // Lancer toutes les requêtes en parallèle (via async si supporté par le client)
        $promises = [];
        foreach ($childDevices as $device) {
            $promises[] = [
                'device' => $device,
                'startTime' => microtime(true),
            ];
        }

        // Attendre au minimum 100ms
        usleep(self::FIRST_RESPONSE_WAIT_MS * 1000);

        // Récupérer la première réponse valide
        foreach ($promises as $promise) {
            $device = $promise['device'];

            try {
                $result = $this->protocolClient->executeAction(
                    protocolId: $device->getProtocolId()->toString(),
                    nativeId: $device->getNativeId(),
                    capabilityName: $command->capabilityName,
                    action: $command->action,
                    parameters: $command->parameters,
                    timeout: self::TIMEOUT_PER_DEVICE_MS
                );

                if ($result['success'] && $firstResponse === null) {
                    $firstResponse = [
                        'deviceId' => $device->getId()->toString(),
                        'deviceName' => $device->getName()->toString(),
                        'success' => true,
                        'response' => $result['response'] ?? null,
                        'responseTime' => round((microtime(true) - $promise['startTime']) * 1000, 2) . 'ms',
                    ];
                    break; // On a notre première réponse, on arrête
                }
            } catch (\Throwable) {
                // On ignore les erreurs et continue jusqu'à avoir une réponse valide
                continue;
            }
        }

        return [
            'success' => $firstResponse !== null,
            'strategy' => 'first_response',
            'firstResponse' => $firstResponse,
            'totalDevices' => count($childDevices),
        ];
    }

    /**
     * Exécute l'action sur tous les devices séquentiellement
     */
    private function executeSequential(Device $composite, ExecuteDeviceAction $command): array
    {
        $this->logger->debug('Executing sequential strategy', [
            'compositeId' => $composite->id->toString(),
        ]);

        $childDevices = $this->loadChildDevices($composite);
        $results = [];

        foreach ($childDevices as $device) {
            try {
                $result = $this->protocolClient->executeAction(
                    protocolId: $device->protocolId->toString(),
                    nativeId: $device->getNativeId(),
                    capabilityName: $command->capabilityName,
                    action: $command->action,
                    parameters: $command->parameters,
                    timeout: self::TIMEOUT_PER_DEVICE_MS
                );

                $results[] = [
                    'deviceId' => $device->id->toString(),
                    'deviceName' => $device->label->value,
                    'success' => $result['success'],
                    'response' => $result['response'] ?? null,
                    'error' => $result['error'] ?? null,
                ];
            } catch (\Throwable $e) {
                $results[] = [
                    'deviceId' => $device->id->toString(),
                    'deviceName' => $device->label->value,
                    'success' => false,
                    'response' => null,
                    'error' => $e->getMessage(),
                ];
            }
        }

        $successCount = count(array_filter($results, fn ($r) => $r['success']));

        return [
            'success' => $successCount > 0,
            'strategy' => 'sequential',
            'successCount' => $successCount,
            'totalCount' => count($childDevices),
            'results' => $results,
        ];
    }

    /**
     * Envoie l'action en broadcast (tous en même temps, sans attendre les réponses)
     */
    private function executeBroadcast(Device $composite, ExecuteDeviceAction $command): array
    {
        $this->logger->debug('Executing broadcast strategy', [
            'compositeId' => $composite->id->toString(),
        ]);

        $childDevices = $this->loadChildDevices($composite);
        $sentCount = 0;

        foreach ($childDevices as $device) {
            try {
                $this->protocolClient->executeAction(
                    protocolId: $device->protocolId->toString(),
                    nativeId: $device->nativeId,
                    capabilityName: $command->capabilityName,
                    action: $command->action,
                    parameters: $command->parameters,
                    timeout: 0 // Pas d'attente de réponse
                );

                $sentCount++;
            } catch (\Throwable $e) {
                $this->logger->warning('Broadcast failed for device', [
                    'deviceId' => $device->id->toString(),
                    'error' => $e->getMessage(),
                ]);
            }
        }

        return [
            'success' => $sentCount > 0,
            'strategy' => 'broadcast',
            'sentCount' => $sentCount,
            'totalCount' => count($childDevices),
        ];
    }

    /**
     * Charge tous les child devices d'un composite
     *
     * @return Device[]
     */
    private function loadChildDevices(Device $composite): array
    {
        $childDeviceIds = array_map(
            fn ($id) => $id->toString(),
            $composite->childDeviceIds
        );

        return $this->deviceRepository->byIds($childDeviceIds);
    }
}

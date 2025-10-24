<?php

namespace Marvin\Device\Application\CommandHandler\Scene;

use EnderLab\DddCqrsBundle\Application\Command\SyncCommandHandlerInterface;
use Marvin\Device\Application\Command\Scene\StoreSceneCurrentState;
use Marvin\Device\Domain\Model\Device;
use Marvin\Device\Domain\Repository\DeviceRepositoryInterface;
use Psr\Log\LoggerInterface;
use Symfony\Component\Messenger\Attribute\AsMessageHandler;

#[AsMessageHandler]
final readonly class StoreSceneCurrentStateHandler
{
    public function __construct(
        private DeviceRepositoryInterface $deviceRepository,
        private LoggerInterface $logger,
    ) {
    }

    public function __invoke(StoreSceneCurrentState $command): void
    {
        $this->logger->info('Storing scene current state', [
            'sceneId' => $command->sceneId,
        ]);

        // Récupérer la scène
        $scene = $this->deviceRepository->byId($command->sceneId);

        // Récupérer tous les devices de la scène
        $deviceIds = $scene->childDeviceIds;
        /** @var Device[] $devices */
        $devices = $this->deviceRepository->byIds(
            array_map(fn ($id) => $id->toString(), $deviceIds)
        );

        // Construire les nouveaux états
        $newSceneStates = [];
        $devicesNotResponding = [];

        foreach ($devices as $device) {
            $deviceId = $device->id;

            try {
                // Capturer l'état actuel de chaque capability
                $deviceStates = [];

                foreach ($device->capabilities as $capability) {
                    $capabilityName = $capability->capability;
                    $capabilityStates = [];

                    // Récupérer tous les états supportés par cette capability
                    foreach ($capability->supportedStates as $stateName) {
                        $currentValue = $device->getState($capabilityName);

                        if ($currentValue !== null) {
                            $capabilityStates[$stateName] = $currentValue;
                        }
                    }

                    if (!empty($capabilityStates)) {
                        $deviceStates[$capabilityName->value] = $capabilityStates;
                    }
                }

                if (!empty($deviceStates)) {
                    $newSceneStates[$deviceId->toString()] = $deviceStates;
                } else {
                    $devicesNotResponding[] = $device->label->value;
                }
            } catch (\Throwable $e) {
                $this->logger->warning('Failed to capture device state', [
                    'deviceId' => $deviceId,
                    'deviceName' => $device->label->value,
                    'error' => $e->getMessage(),
                ]);

                $devicesNotResponding[] = $device->label->value;
            }
        }

        // Mettre à jour les états de la scène
        $scene->setSceneStates($newSceneStates);
        $this->deviceRepository->save($scene);

        $this->logger->info('Scene current state stored', [
            'sceneId' => $command->sceneId,
            'devicesStored' => count($newSceneStates),
            'devicesNotResponding' => count($devicesNotResponding),
        ]);

        if (!empty($devicesNotResponding)) {
            $this->logger->warning('Some devices did not respond', [
                'devices' => $devicesNotResponding,
            ]);
        }
    }
}

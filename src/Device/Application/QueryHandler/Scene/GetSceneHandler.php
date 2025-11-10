<?php

declare(strict_types=1);

namespace Marvin\Device\Application\QueryHandler\Scene;

use EnderLab\DddCqrsBundle\Application\Query\QueryHandlerInterface;
use InvalidArgumentException;
use Marvin\Device\Application\Query\Scene\GetScene;
use Marvin\Device\Domain\Exception\DeviceNotFoundException;
use Marvin\Device\Domain\Model\Device;
use Marvin\Device\Domain\Repository\DeviceRepositoryInterface;
use Marvin\Device\Domain\ValueObject\CompositeType;
use Symfony\Component\Messenger\Attribute\AsMessageHandler;

/**
 * Handler for GetScene query
 */
#[AsMessageHandler]
final readonly class GetSceneHandler implements QueryHandlerInterface
{
    public function __construct(
        private DeviceRepositoryInterface $deviceRepository
    ) {
    }

    public function __invoke(GetScene $query): Device
    {
        $scene = $this->deviceRepository->byId($query->sceneId);

        if ($scene === null) {
            throw DeviceNotFoundException::withId($query->sceneId);
        }

        // Verify it's actually a scene
        if (!$scene->isComposite() || $scene->compositeType !== CompositeType::SCENE) {
            throw new InvalidArgumentException(sprintf(
                'Device %s is not a scene',
                $query->sceneId->toString()
            ));
        }

        return $scene;
    }
}

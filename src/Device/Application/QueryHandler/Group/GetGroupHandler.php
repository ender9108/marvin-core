<?php
/**
 * Marvin Core - DDD-based home automation system
 *
 * @package   Marvin\Core
 * @author    Alexandre Berthelot <alexandreberthelot9108@gmail.com>
 * @copyright 2024-present Alexandre Berthelot
 * @license   AGPL-3.0 License
 * @link      https://github.com/ender9108/marvin-core
 */
declare(strict_types=1);

namespace Marvin\Device\Application\QueryHandler\Group;

use EnderLab\DddCqrsBundle\Application\Query\QueryHandlerInterface;
use InvalidArgumentException;
use Marvin\Device\Application\Query\Group\GetGroup;
use Marvin\Device\Domain\Exception\DeviceNotFoundException;
use Marvin\Device\Domain\Model\Device;
use Marvin\Device\Domain\Repository\DeviceRepositoryInterface;
use Marvin\Device\Domain\ValueObject\CompositeType;
use Symfony\Component\Messenger\Attribute\AsMessageHandler;

/**
 * Handler for GetGroup query
 */
#[AsMessageHandler]
final readonly class GetGroupHandler implements QueryHandlerInterface
{
    public function __construct(
        private DeviceRepositoryInterface $deviceRepository
    ) {
    }

    public function __invoke(GetGroup $query): Device
    {
        $group = $this->deviceRepository->byId($query->groupId);

        if ($group === null) {
            throw DeviceNotFoundException::withId($query->groupId);
        }

        // Verify it's actually a group
        if (!$group->isComposite() || $group->compositeType !== CompositeType::GROUP) {
            throw new InvalidArgumentException(sprintf(
                'Device %s is not a group',
                $query->groupId->toString()
            ));
        }

        return $group;
    }
}

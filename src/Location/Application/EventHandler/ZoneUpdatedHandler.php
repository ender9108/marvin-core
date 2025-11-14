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

namespace Marvin\Location\Application\EventHandler;

use Marvin\Location\Domain\Event\Zone\ZoneUpdated;
use Symfony\Component\Messenger\Attribute\AsMessageHandler;

#[AsMessageHandler]
final readonly class ZoneUpdatedHandler
{
    public function __invoke(ZoneUpdated $event): void
    {
        dump('Zone updated: ' . $event->zoneId);
    }
}

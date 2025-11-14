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

namespace Marvin\Shared\Domain\Event\Location;

use EnderLab\DddCqrsBundle\Domain\Event\AbstractDomainEvent;

final readonly class ZoneSlugUpdated extends AbstractDomainEvent
{
    public function __construct(
        public string $zoneId,
        public string $zoneLabel,
        public string $zoneSlug,
    ) {
        parent::__construct();
    }

    public function toArray(): array
    {
        return [
            'zone_id' => $this->zoneId,
            'zone_label' => $this->zoneLabel,
            'zone_slug' => $this->zoneSlug,
            'occurred_at' => $this->occurredOn->format('c'),
        ];
    }
}

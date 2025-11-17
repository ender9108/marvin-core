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

namespace Marvin\Location\Domain\Exception;

use EnderLab\DddCqrsBundle\Domain\Exception\DomainException;
use EnderLab\DddCqrsBundle\Domain\Exception\Interfaces\TranslatableExceptionInterface;
use Marvin\Location\Domain\ValueObject\ZoneName;
use Marvin\Location\Domain\ValueObject\ZoneType;
use Marvin\Shared\Domain\ValueObject\Identity\ZoneId;

final class InvalidZoneHierarchy extends DomainException implements TranslatableExceptionInterface
{
    public function __construct(
        string $message,
        public readonly ?string $zoneName = null,
        public readonly ?string $type = null,
        public readonly ?string $parentId = null,
        public readonly ?int $childrenCount = null,
    ) {
        parent::__construct($message);
    }

    public static function cannotHaveChildren(ZoneName $zoneName, ZoneType $type): self
    {
        return new self(
            sprintf('Zone parent %s of type %s cannot have children', $zoneName, $type->value),
            $zoneName,
            $type->name,
        );
    }

    public static function circularReference(ZoneName $zoneName): self
    {
        return new self(
            sprintf('Cannot create circular reference: zone %s cannot be its own parent', $zoneName),
            $zoneName
        );
    }

    public static function parentNotFound(ZoneId $parentId): self
    {
        return new self(
            sprintf('Parent zone with id %s not found', $parentId),
            null,
            null,
            $parentId,
        );
    }

    public static function cannotDeleteZoneWithChildren(ZoneName $zoneName, int $childrenCount): self
    {
        return new self(
            sprintf('Cannot delete zone %s because it has %d children', $zoneName, $childrenCount),
            $zoneName,
            null,
            null,
            $childrenCount,
        );
    }

    #[Override]
    public function translationId(): string
    {
        if (null !== $this->zoneName && null !== $this->type && null === $this->parentId && null === $this->childrenCount) {
            return 'location.exceptions.LO0008.zone_cannot_have_children';
        }

        if (null !== $this->zoneName && null === $this->type && null === $this->parentId && null === $this->childrenCount) {
            return 'location.exceptions.LO0009.zone_circular_reference';
        }

        if (null === $this->zoneName && null === $this->type && null !== $this->parentId && null === $this->childrenCount) {
            return 'location.exceptions.LO0004.zone_parent_not_found_with_id';
        }

        if (null !== $this->zoneName && null === $this->type && null === $this->parentId && null !== $this->childrenCount) {
            return 'location.exceptions.LO0010.zone_cannot_delete_zone_with_children';
        }

        return 'location.exceptions.LO0007.zone_hierarchy_invalid';
    }

    #[Override]
    /** @return array<string, string|null> */
    public function translationParameters(): array
    {
        return [
            '%name%' => $this->zoneName,
            '%type%' => $this->type,
            '%parentId%' => $this->parentId,
            '%childrenCount%' => $this->childrenCount,
        ];
    }

    #[Override]
    public function translationDomain(): string
    {
        return 'location';
    }
}

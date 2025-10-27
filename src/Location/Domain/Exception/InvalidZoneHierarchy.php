<?php

namespace Marvin\Location\Domain\Exception;

use EnderLab\DddCqrsBundle\Domain\Exception\DomainException;
use EnderLab\DddCqrsBundle\Domain\Exception\TranslatableExceptionInterface;
use Marvin\Location\Domain\ValueObject\ZoneType;
use Marvin\Shared\Domain\ValueObject\Identity\ZoneId;
use Marvin\Shared\Domain\ValueObject\Label;

final class InvalidZoneHierarchy extends DomainException implements TranslatableExceptionInterface
{
    public function __construct(
        string $message,
        string $code,
        public readonly ?string $label = null,
        public readonly ?string $type = null,
        public readonly ?string $parentId = null,
        public readonly ?int $childrenCount = null,
    ) {
        parent::__construct($message, $code);
    }

    public static function cannotHaveChildren(Label $label, ZoneType $type): self
    {
        return new self(
            sprintf('Zone %s of type %s cannot have children', $label, $type->value),
            'LO00001',
            $label,
            $type->name,
        );
    }

    public static function circularReference(Label $label): self
    {
        return new self(
            sprintf('Cannot create circular reference: zone % cannot be its own parent', $label),
            'LO00002',
            $label
        );
    }

    public static function parentNotFound(ZoneId $parentId): self
    {
        return new self(
            sprintf('Parent zone with id %s not found', $parentId),
            'LO00003',
            null,
            null,
            $parentId,
        );
    }

    public static function cannotDeleteZoneWithChildren(Label $label, int $childrenCount): self
    {
        return new self(
            sprintf('Cannot delete zone %s because it has %d children', $label, $childrenCount),
            'LO00004',
            $label,
            null,
            null,
            $childrenCount,
        );
    }

    #[Override]
    public function translationId(): string
    {
        if (null !== $this->label && null !== $this->type && null === $this->parentId && null === $this->childrenCount) {
            return 'location.exceptions.zone_cannot_have_children';
        }

        if (null !== $this->label && null === $this->type && null === $this->parentId && null === $this->childrenCount) {
            return 'location.exceptions.zone_circular_reference';
        }

        if (null === $this->label && null === $this->type && null !== $this->parentId && null === $this->childrenCount) {
            return 'location.exceptions.zone_parent_not_found';
        }

        if (null !== $this->label && null === $this->type && null === $this->parentId && null !== $this->childrenCount) {
            return 'location.exceptions.zone_cannot_delete_zone_with_children';
        }


        return 'location.exceptions.zone_hierarchy_invalid';
    }

    #[Override]
    /** @return array<string, string|null> */
    public function translationParameters(): array
    {
        return [
            '%name%' => $this->label,
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

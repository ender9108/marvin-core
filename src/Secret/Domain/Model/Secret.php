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

namespace Marvin\Secret\Domain\Model;

use DateMalformedStringException;
use DateTimeImmutable;
use DateTimeInterface;
use EnderLab\DddCqrsBundle\Domain\Model\AggregateRoot;
use Marvin\Secret\Domain\Event\SecretCreated;
use Marvin\Secret\Domain\Event\SecretDeleted;
use Marvin\Secret\Domain\Event\SecretRotated;
use Marvin\Secret\Domain\Event\SecretUpdated;
use Marvin\Secret\Domain\ValueObject\Identity\SecretId;
use Marvin\Secret\Domain\ValueObject\RotationPolicy;
use Marvin\Secret\Domain\ValueObject\SecretCategory;
use Marvin\Secret\Domain\ValueObject\SecretKey;
use Marvin\Secret\Domain\ValueObject\SecretScope;
use Marvin\Secret\Domain\ValueObject\SecretValue;
use Marvin\Shared\Domain\ValueObject\Metadata;

class Secret extends AggregateRoot
{
    public function __construct(
        private(set) SecretKey $key,
        private(set) SecretValue $value,
        private(set) SecretScope $scope,
        private(set) SecretCategory $category,
        private(set) ?RotationPolicy $rotationPolicy = null,
        private(set) ?DateTimeInterface $lastRotatedAt = null,
        private(set) ?DateTimeInterface $expiresAt = null,
        private(set) ?Metadata $metadata = null,
        private(set) ?DateTimeInterface $updatedAt = null,
        public readonly DateTimeInterface $createdAt = new DateTimeImmutable(),
        private(set) SecretId $id = new SecretId(),
    ) {
        $this->recordEvent(
            new SecretCreated(
                secretId: $this->id->toString(),
                key: $this->key->value,
                scope: $this->scope->value,
                category: $this->category->value,
                autoRotate: $this->rotationPolicy->isAutoRotate(),
                rotationIntervalDays: $this->rotationPolicy->getRotationIntervalDays(),
            )
        );
    }

    public static function create(
        SecretKey $key,
        SecretValue $value,
        SecretScope $scope,
        SecretCategory $category,
        RotationPolicy $rotationPolicy,
        ?DateTimeInterface $expiresAt = null,
        ?Metadata $metadata = null,
    ): self {
        $now = new DateTimeImmutable();

        return new self(
            key: $key,
            value: $value,
            scope: $scope,
            category: $category,
            rotationPolicy: $rotationPolicy,
            lastRotatedAt: null,
            expiresAt: $expiresAt,
            metadata: $metadata,
        );
    }

    public function updateValue(SecretValue $newValue): void
    {
        $this->value = $newValue;

        $this->recordEvent(
            new SecretUpdated(
                secretId: $this->id->toString(),
                key: $this->key->value,
                scope: $this->scope->value,
                category: $this->category->value,
                valueChanged: true,
            )
        );
    }

    public function rotate(SecretValue $newValue): void
    {
        $previousValueHash = hash('sha256', $this->value->getEncrypted());
        $metadata = [];
        $metadata['previous_value'] = $this->value->getEncrypted();
        $metadata['previous_value_rotated_at'] = new DateTimeImmutable()->format('c');
        $this->metadata = Metadata::fromArray(array_merge(
            null === $this->metadata ? [] : $this->metadata->toArray(),
            $metadata
        ));

        $this->value = $newValue;
        $this->lastRotatedAt = new DateTimeImmutable();

        $this->recordEvent(
            new SecretRotated(
                secretId: $this->id->toString(),
                key: $this->key->value,
                scope: $this->scope->value,
                category: $this->category->value,
                automatic: true,
                previousValueHash: $previousValueHash,
            )
        );
    }

    public function delete(): void
    {
        $this->recordEvent(
            new SecretDeleted(
                secretId: $this->id->toString(),
                key: $this->key->value,
                scope: $this->scope->value,
                category: $this->category->value,
            )
        );
    }

    public function isExpired(): bool
    {
        if ($this->expiresAt === null) {
            return false;
        }

        return $this->expiresAt < new DateTimeImmutable();
    }

    /**
     * @throws DateMalformedStringException
     */
    public function needsRotation(): bool
    {
        if ($this->lastRotatedAt === null) {
            return false; // Jamais tourné = pas besoin
        }

        return $this->rotationPolicy->shouldRotate($this->lastRotatedAt);
    }

    public function updateRotationPolicy(RotationPolicy $policy): void
    {
        $this->rotationPolicy = $policy;
        $this->updatedAt = new DateTimeImmutable();
    }
}

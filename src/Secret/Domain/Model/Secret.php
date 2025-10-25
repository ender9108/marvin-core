<?php

namespace Marvin\Secret\Domain\Model;

use DateTimeImmutable;
use DateTimeInterface;
use EnderLab\DddCqrsBundle\Domain\Model\AggregateRoot;
use Marvin\Secret\Domain\ValueObject\Identity\SecretId;
use Marvin\Secret\Domain\ValueObject\RotationPolicy;
use Marvin\Secret\Domain\ValueObject\SecretCategory;
use Marvin\Secret\Domain\ValueObject\SecretKey;
use Marvin\Secret\Domain\ValueObject\SecretScope;
use Marvin\Secret\Domain\ValueObject\SecretValue;
use Marvin\Shared\Domain\ValueObject\Identity\UniqId;
use Marvin\Shared\Domain\ValueObject\Metadata;

class Secret extends AggregateRoot
{
    public private(set) SecretId $id;

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
    ) {
        $this->id = new SecretId();
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
        $this->updatedAt = new DateTimeImmutable();
    }

    public function rotate(SecretValue $newValue): void
    {
        // Garde l'ancienne valeur dans metadata pour période de transition
        $this->metadata['previous_value'] = $this->value->getEncrypted();
        $this->metadata['previous_value_rotated_at'] = new DateTimeImmutable()->format('c');

        // Rotation simple: met à jour la valeur et les timestamps
        $this->value = $newValue;
        $this->lastRotatedAt = new DateTimeImmutable();
    }

    public function isExpired(): bool
    {
        if ($this->expiresAt === null) {
            return false;
        }

        return $this->expiresAt < new DateTimeImmutable();
    }

    /**
     * @throws \DateMalformedStringException
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

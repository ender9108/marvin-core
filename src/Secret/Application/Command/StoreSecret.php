<?php

namespace Marvin\Secret\Application\Command;

use DateTimeImmutable;
use EnderLab\DddCqrsBundle\Application\Command\SyncCommandInterface;
use Marvin\Secret\Domain\ValueObject\SecretCategory;
use Marvin\Secret\Domain\ValueObject\SecretKey;
use Marvin\Secret\Domain\ValueObject\SecretScope;
use Marvin\Shared\Domain\ValueObject\Metadata;

final readonly class StoreSecret implements SyncCommandInterface
{
    public function __construct(
        public SecretKey $key,
        public string $plainTextValue,
        public SecretScope $scope = SecretScope::GLOBAL,
        public SecretCategory $category = SecretCategory::INFRASTRUCTURE,
        public bool $managed = false,
        public int $rotationIntervalDays = 0,
        public bool $autoRotate = false,
        public ?string $rotationCommand = null,
        public ?DateTimeImmutable $expiresAt = null,
        public ?Metadata $metadata = null,
    ) {
    }
}

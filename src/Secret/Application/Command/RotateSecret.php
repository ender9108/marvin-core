<?php

namespace Marvin\Secret\Application\Command;

use EnderLab\DddCqrsBundle\Application\Command\SyncCommandInterface;
use Marvin\Secret\Domain\ValueObject\SecretKey;

final readonly class RotateSecret implements SyncCommandInterface
{
    public function __construct(
        public SecretKey $key,
        public ?string $newValue = null, // Si null, génère automatiquement
    ) {
    }
}

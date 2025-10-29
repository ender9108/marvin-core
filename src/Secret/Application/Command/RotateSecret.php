<?php

namespace Marvin\Secret\Application\Command;

use EnderLab\DddCqrsBundle\Application\Command\CommandInterface;
use Marvin\Secret\Domain\ValueObject\SecretKey;

final readonly class RotateSecret implements CommandInterface
{
    public function __construct(
        public SecretKey $key,
        public ?string $newValue = null, // Si null, génère automatiquement
    ) {
    }
}

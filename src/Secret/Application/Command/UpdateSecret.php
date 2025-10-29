<?php

namespace Marvin\Secret\Application\Command;

use EnderLab\DddCqrsBundle\Application\Command\SyncCommandInterface;
use Marvin\Secret\Domain\ValueObject\SecretKey;

final readonly class UpdateSecret implements SyncCommandInterface
{
    public function __construct(
        public SecretKey $key,
        public string $newValue,
    ) {
    }
}

<?php

declare(strict_types=1);

namespace Marvin\Protocol\Application\Command;

use EnderLab\DddCqrsBundle\Application\Command\CommandInterface;
use Marvin\Protocol\Domain\ValueObject\ExecutionMode;
use Marvin\Shared\Domain\ValueObject\Identity\DeviceId;

final readonly class SendDeviceCommand implements CommandInterface
{
    public function __construct(
        public DeviceId $deviceId,
        public string $action,
        public array $parameters = [],
        public ?ExecutionMode $executionMode = null,
    ) {
    }
}

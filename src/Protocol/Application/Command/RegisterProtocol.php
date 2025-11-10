<?php

declare(strict_types=1);

namespace Marvin\Protocol\Application\Command;

use EnderLab\DddCqrsBundle\Application\Command\CommandInterface;
use Marvin\Protocol\Domain\ValueObject\ExecutionMode;
use Marvin\Protocol\Domain\ValueObject\ProtocolConfiguration;
use Marvin\Protocol\Domain\ValueObject\TransportType;
use Marvin\Shared\Domain\ValueObject\Label;

final readonly class RegisterProtocol implements CommandInterface
{
    public function __construct(
        public TransportType $type,
        public Label $name,
        public ProtocolConfiguration $configuration,
        public ?ExecutionMode $preferredExecutionMode = null,
    ) {
    }
}

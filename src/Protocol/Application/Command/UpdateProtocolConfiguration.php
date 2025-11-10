<?php

declare(strict_types=1);

namespace Marvin\Protocol\Application\Command;

use EnderLab\DddCqrsBundle\Application\Command\CommandInterface;
use Marvin\Protocol\Domain\ValueObject\ExecutionMode;
use Marvin\Protocol\Domain\ValueObject\ProtocolConfiguration;
use Marvin\Shared\Domain\ValueObject\Identity\ProtocolId;

final readonly class UpdateProtocolConfiguration implements CommandInterface
{
    public function __construct(
        public ProtocolId $protocolId,
        public ProtocolConfiguration $configuration,
        public ?ExecutionMode $preferredExecutionMode = null,
    ) {
    }
}

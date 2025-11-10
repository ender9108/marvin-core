<?php

declare(strict_types=1);

namespace Marvin\Device\Application\Command\PendingAction;

use EnderLab\DddCqrsBundle\Application\Command\CommandInterface;
use Marvin\Shared\Domain\ValueObject\Identity\CorrelationId;

/**
 * FailPendingAction - Command
 *
 * Marks a pending action as failed with error message
 */
final readonly class FailPendingAction implements CommandInterface
{
    public function __construct(
        public CorrelationId $correlationId,
        public string $errorMessage,
    ) {
    }
}

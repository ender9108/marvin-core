<?php

declare(strict_types=1);

namespace Marvin\Shared\Domain\ValueObject\Identity;

use Symfony\Component\Uid\UuidV7;

/**
 * PendingActionId - Unique identifier for PendingAction aggregates
 *
 * Used to track device actions waiting for completion or response
 */
final class PendingActionId extends UuidV7
{
}

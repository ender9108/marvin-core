<?php

declare(strict_types=1);

namespace Marvin\Device\Domain\Exception;

use Marvin\Device\Domain\ValueObject\CapabilityState;

/**
 * Exception levée quand le type de données d'un CapabilityState n'est pas encore implémenté
 */
final class CapabilityStateDataTypeNotImplemented extends \RuntimeException
{
    public static function withType(CapabilityState $state): self
    {
        return new self(sprintf(
            'Data type for capability state "%s" is not yet implemented',
            $state->value
        ));
    }
}

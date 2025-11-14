<?php
/**
 * Marvin Core - DDD-based home automation system
 *
 * @package   Marvin\Core
 * @author    Alexandre Berthelot <alexandreberthelot9108@gmail.com>
 * @copyright 2024-present Alexandre Berthelot
 * @license   AGPL-3.0 License
 * @link      https://github.com/ender9108/marvin-core
 */
declare(strict_types=1);

namespace Marvin\Device\Domain\Exception;

use RuntimeException;
use Marvin\Device\Domain\ValueObject\CapabilityState;

/**
 * Exception levée quand le type de données d'un CapabilityState n'est pas encore implémenté
 */
final class CapabilityStateDataTypeNotImplemented extends RuntimeException
{
    public static function withType(CapabilityState $state): self
    {
        return new self(sprintf(
            'Data type for capability state "%s" is not yet implemented',
            $state->value
        ));
    }
}

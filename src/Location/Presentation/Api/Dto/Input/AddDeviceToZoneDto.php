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

namespace Marvin\Location\Presentation\Api\Dto\Input;

use Symfony\Component\Validator\Constraints\Uuid;

final class AddDeviceToZoneDto
{
    #[Uuid(versions: Uuid::V7_MONOTONIC)]
    public string $deviceId;
}

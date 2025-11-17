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

namespace Marvin\Secret\Presentation\Api\Dto;

use Symfony\Component\Validator\Constraints\Regex;

final class RotateSecretDto
{
    #[Regex('/^[a-zA-Z0-9_.:-]{3,128}$/')]
    public string $key;

    public ?string $newValue = null;
}

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

namespace Marvin\Shared\Application\Service\Acl\Dto;

final readonly class ConnectedUserDto
{
    public function __construct(
        public ?string $id,
        public ?string $fullname,
        public ?string $locale,
        public ?string $timezone,
        public ?string $theme,
        public ?array $roles,
    ) {
    }
}

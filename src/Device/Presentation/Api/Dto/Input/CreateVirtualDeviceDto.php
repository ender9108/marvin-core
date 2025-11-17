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

namespace Marvin\Device\Presentation\Api\Dto\Input;

use Marvin\Device\Domain\ValueObject\VirtualDeviceType;
use Symfony\Component\Validator\Constraints\Choice;
use Symfony\Component\Validator\Constraints\NotBlank;
use Symfony\Component\Validator\Constraints\Uuid;

final class CreateVirtualDeviceDto
{
    #[NotBlank]
    public string $label;

    #[NotBlank]
    #[Choice(callback: [VirtualDeviceType::class, 'values'])]
    public string $virtualType;

    #[NotBlank]
    public array $virtualConfig;

    public array $capabilities = [];

    #[Uuid(versions: Uuid::V7_MONOTONIC)]
    public ?string $zoneId = null;

    public ?string $description = null;

    public ?array $metadata = null;
}

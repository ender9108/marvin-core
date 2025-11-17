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

use Marvin\Device\Domain\ValueObject\DeviceType;
use Marvin\Device\Domain\ValueObject\Protocol;
use Symfony\Component\Validator\Constraints\Choice;
use Symfony\Component\Validator\Constraints\NotBlank;
use Symfony\Component\Validator\Constraints\Uuid;

final class CreatePhysicalDeviceDto
{
    #[NotBlank]
    public string $label;

    #[NotBlank]
    #[Choice(choices: [
        DeviceType::ACTUATOR->value,
        DeviceType::SENSOR->value,
    ])]
    public string $deviceType;

    #[NotBlank]
    #[Choice(choices: [
        Protocol::ZIGBEE->value,
        Protocol::BLUETOOTH->value,
        Protocol::NETWORK->value,
    ])]
    public string $protocol;

    #[NotBlank]
    #[Uuid(versions: Uuid::V7_MONOTONIC)]
    public string $protocolId;

    #[NotBlank]
    public string $physicalAddress;

    #[NotBlank]
    public string $technicalName;

    public array $capabilities = [];

    #[Uuid(versions: Uuid::V7_MONOTONIC)]
    public ?string $zoneId = null;

    public ?string $description = null;

    public ?array $metadata = null;
}

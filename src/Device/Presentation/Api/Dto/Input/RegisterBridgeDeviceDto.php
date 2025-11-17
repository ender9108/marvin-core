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

use Marvin\Device\Domain\ValueObject\Protocol;
use Symfony\Component\Validator\Constraints as Assert;

/**
 * DTO for registering a new bridge/coordinator device
 */
final class RegisterBridgeDeviceDto
{
    #[Assert\NotBlank]
    #[Assert\Length(min: 1, max: 255)]
    public string $label;

    #[Assert\NotBlank]
    #[Assert\Choice(choices: [
        Protocol::ZIGBEE->value,
        Protocol::MQTT->value,
        Protocol::NETWORK->value,
        Protocol::BLUETOOTH->value,
        Protocol::MATTER->value,
        Protocol::THREAD->value,
        Protocol::ZWAVE->value,
    ])]
    public string $protocol;

    #[Assert\NotBlank]
    #[Assert\Uuid]
    public string $protocolId;

    #[Assert\NotBlank]
    #[Assert\Length(min: 1, max: 255)]
    public string $physicalAddress;

    #[Assert\NotBlank]
    #[Assert\Length(min: 1, max: 255)]
    public string $technicalName;

    #[Assert\Type('array')]
    public array $coordinatorInfo = [];

    #[Assert\Type('array')]
    public array $networkTopology = [];

    #[Assert\Length(max: 1000)]
    public ?string $description = null;

    #[Assert\Type('array')]
    public ?array $metadata = null;
}

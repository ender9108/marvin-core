<?php

declare(strict_types=1);

namespace Marvin\Device\Domain\ValueObject;

use EnderLab\DddCqrsBundle\Domain\Assert\Assert;
use EnderLab\DddCqrsBundle\Domain\ValueObject\ValueObjectTrait;
use Stringable;

/**
 * PhysicalAddress - Adresse physique du device dans son protocol
 *
 * Représente l'identifiant unique du device au niveau protocol :
 * - Zigbee : IEEE address (ex: "0x00158d0001234567")
 * - WiFi : IP address (ex: "192.168.1.100")
 * - Bluetooth : MAC address (ex: "AA:BB:CC:DD:EE:FF")
 * - MQTT : Topic ID (ex: "tasmota_ABC123")
 */
final readonly class PhysicalAddress implements Stringable
{
    use ValueObjectTrait;

    public string $value;

    private function __construct(string $value)
    {
        Assert::notEmpty($value, 'Physical address cannot be empty');
        Assert::maxLength($value, 100, 'Physical address cannot exceed 100 characters');

        $this->value = $value;
    }

    public static function fromString(string $value): self
    {
        return new self($value);
    }

    public function __toString(): string
    {
        return $this->value;
    }
}

<?php

declare(strict_types=1);

namespace Marvin\Device\Domain\ValueObject;

use EnderLab\DddCqrsBundle\Domain\Assert\Assert;
use EnderLab\DddCqrsBundle\Domain\ValueObject\ValueObjectTrait;
use Stringable;

/**
 * TechnicalName - Nom technique utilisé dans les protocols
 *
 * Utilisé comme :
 * - friendly_name dans Zigbee2MQTT (ex: "lamp_salon")
 * - topic dans MQTT (ex: "tasmota_lamp1")
 * - device name dans configurations
 *
 * Contraintes : alphanumérique, underscore, tiret (pas d'espaces)
 */
final readonly class TechnicalName implements Stringable
{
    use ValueObjectTrait;

    public string $value;

    private function __construct(string $value)
    {
        Assert::notEmpty($value, 'Technical name cannot be empty');
        Assert::maxLength($value, 100, 'Technical name cannot exceed 100 characters');
        Assert::regex(
            $value,
            '/^[a-z0-9_-]+$/',
            'Technical name must contain only lowercase letters, numbers, underscores and hyphens'
        );

        $this->value = $value;
    }

    public static function fromString(string $value): self
    {
        return new self(strtolower(trim($value)));
    }

    /**
     * Génère un nom technique depuis un label humain
     * Ex: "Lampe Salon" → "lampe_salon"
     */
    public static function fromLabel(string $label): self
    {
        $technical = strtolower(trim($label));
        $technical = preg_replace('/[^a-z0-9]+/', '_', $technical);
        $technical = trim((string) $technical, '_');

        return new self($technical);
    }

    public function __toString(): string
    {
        return $this->value;
    }
}

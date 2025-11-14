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

use EnderLab\DddCqrsBundle\Domain\Exception\DomainException;
use EnderLab\DddCqrsBundle\Domain\Exception\Interfaces\TranslatableExceptionInterface;

class InvalidVirtualConfig extends DomainException implements TranslatableExceptionInterface
{
    public function __construct(
        string $message,
        public readonly ?string $key = null,
        public readonly mixed $value = null,
        public readonly mixed $virtualType = null,
    ) {
        parent::__construct($message);
    }

    public static function missingKeys(string $virtualType, array $requiredKeys): self
    {
        return new self(
            sprintf(
                'Virtual device type %s requires configuration keys: %s',
                $virtualType,
                implode(', ', $requiredKeys)
            ),
            implode(', ', $requiredKeys),
            null,
            $virtualType
        );
    }

    public static function invalidValue(string $key, mixed $value): self
    {
        return new self(
            sprintf('Invalid value for key %s: %s', $key, $value),
            $key,
            $value
        );
    }

    public function translationId(): string
    {
        if (null !== $this->key && null !== $this->value) {
            return 'device.exceptions.DE0021.invalid_virtual_config_with_key_value';
        }

        if (null !== $this->key && null === $this->value && null !== $this->virtualType) {
            return 'device.exceptions.DE0039.invalid_virtual_config_with_missing_key';
        }

        return 'device.exceptions.DE0020.invalid_virtual_config';
    }

    public function translationParameters(): array
    {
        return [
            '%key%' => $this->key,
            '%value%' => $this->value,
            '%virtual_type%' => $this->virtualType,
        ];
    }

    public function translationDomain(): string
    {
        return 'device';
    }
}

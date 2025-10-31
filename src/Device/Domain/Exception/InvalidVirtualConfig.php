<?php

namespace Marvin\Device\Domain\Exception;

use EnderLab\DddCqrsBundle\Domain\Exception\DomainException;
use EnderLab\DddCqrsBundle\Domain\Exception\TranslatableExceptionInterface;

class InvalidVirtualConfig extends DomainException implements TranslatableExceptionInterface
{
    public function __construct(
        string $message,
        public readonly ?string $key = null,
        public readonly mixed $value = null,
    ) {
        parent::__construct($message);
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

        return 'device.exceptions.DE0020.invalid_virtual_config';
    }

    public function translationParameters(): array
    {
        return [
            '%key%' => $this->key,
            '%value%' => $this->value,
        ];
    }

    public function translationDomain(): string
    {
        return 'device';
    }
}

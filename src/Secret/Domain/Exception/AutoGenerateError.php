<?php

namespace Marvin\Secret\Domain\Exception;

use EnderLab\DddCqrsBundle\Domain\Exception\DomainException;
use EnderLab\DddCqrsBundle\Domain\Exception\TranslatableExceptionInterface;
use Marvin\Secret\Domain\ValueObject\SecretKey;

class AutoGenerateError extends DomainException implements TranslatableExceptionInterface
{
    public function __construct(
        string $message,
        string $code,
        public readonly ?string $key = null,
    ) {
        parent::__construct($message, $code);
    }

    public static function withKey(SecretKey $key): self {
        return new self(
            sprintf('Cannot auto-generate value for external secret %s. Please provide a new value.', $key->value),
            'ST0005',
            $key->value
        );
    }

    public function translationId(): string
    {
        if (null !== $this->key) {
            return 'secret.exceptions.auto_generate_error_with_key';
        }

        return 'secret.exceptions.auto_generate_error';
    }

    public function translationParameters(): array
    {
        return [
            '%key%' => $this->key
        ];
    }

    public function translationDomain(): string
    {
        return 'secret';
    }
}

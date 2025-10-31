<?php

namespace Marvin\Secret\Domain\Exception;

use EnderLab\DddCqrsBundle\Domain\Exception\DomainException;
use EnderLab\DddCqrsBundle\Domain\Exception\TranslatableExceptionInterface;
use Marvin\Secret\Domain\ValueObject\Identity\SecretId;
use Marvin\Secret\Domain\ValueObject\SecretKey;

final class SecretNotFound extends DomainException implements TranslatableExceptionInterface
{
    public function __construct(
        string $message,
        public readonly ?string $id = null,
        public readonly ?string $key = null,
    ) {
        parent::__construct($message, $code);
    }

    public static function withKey(SecretKey $key): self
    {
        return new self(
            sprintf('Secret with key "%s" not found', $key->value),
            null,
            $key->value
        );
    }

    public static function withId(SecretId $id): self
    {
        return new self(
            sprintf('Secret with id "%s" not found', $id->toString()),
            $id->toString(),
            null
        );
    }

    public function translationId(): string
    {
        if ($this->id !== null) {
            return 'secret.exceptions.SR0015.secret_not_found_with_id';
        }

        if ($this->key !== null) {
            return 'secret.exceptions.SR0016.secret_not_found_with_key';
        }

        return 'secret.exceptions.SR0017.secret_not_found';
    }

    public function translationParameters(): array
    {
        return [
            '%id%' => $this->id,
            '%key%' => $this->key,
        ];
    }

    public function translationDomain(): string
    {
        return 'secret';
    }
}

<?php

namespace Marvin\Secret\Domain\Exception;

use EnderLab\DddCqrsBundle\Domain\Exception\DomainException;
use EnderLab\DddCqrsBundle\Domain\Exception\TranslatableExceptionInterface;

final class EncryptionError extends DomainException implements TranslatableExceptionInterface
{
    public function __construct(
        string $message,
        string $code,
        public readonly bool $masterKeyLength = false,
        public readonly bool $ivLength = false,
        public readonly bool $cipherText = false,
        public readonly bool $decryptionFailedBase64Decode = false,
        public readonly bool $decryptionFailedOpenSsl = false,
    ) {
        parent::__construct($message, $code);
    }

    public static function masterKeyLength(): self
    {
        return new self(
            'The master key length must be 32 characters',
            'S0009',
            true
        );
    }

    public static function ivLength(): self
    {
        return new self(
            'Iv length error. Expected 16 bytes, got false',
            'S0010',
            false,
            true
        );
    }

    public static function cipherText(): self
    {
        return new self(
            'Cipher text error.',
            'S0011',
            false,
            false,
            true
        );
    }

    public static function decryptionFailedBase64Decode(): self
    {
        return new self(
            'Base 64 decode failed.',
            'S0012',
            false,
            false,
            false,
            true
        );
    }

    public static function decryptionFailedOpenSsl(): self
    {
        return new self(
            'Open SSL decrypt failed.',
            'S0013',
            false,
            false,
            false,
            false,
            true
        );
    }

    public function translationId(): string
    {
        if (true === $this->masterKeyLength) {
            return 'secret.exceptions.encryption_error_master_key_length';
        }

        if (true === $this->ivLength) {
            return 'secret.exceptions.encryption_error_iv_length';
        }

        if (true === $this->cipherText) {
            return 'secret.exceptions.encryption_error_cipher_text';
        }

        if (true === $this->decryptionFailedBase64Decode) {
            return 'secret.exceptions.encryption_error_decryption_failed_base64_decode';
        }

        return 'secret.exceptions.encryption_error';
    }

    public function translationParameters(): array
    {
        return [];
    }

    public function translationDomain(): string
    {
        return 'secret';
    }
}

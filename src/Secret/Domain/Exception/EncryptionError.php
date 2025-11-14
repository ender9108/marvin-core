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

namespace Marvin\Secret\Domain\Exception;

use EnderLab\DddCqrsBundle\Domain\Exception\DomainException;
use EnderLab\DddCqrsBundle\Domain\Exception\Interfaces\TranslatableExceptionInterface;

final class EncryptionError extends DomainException implements TranslatableExceptionInterface
{
    public function __construct(
        string $message,
        public readonly bool $masterKeyLength = false,
        public readonly bool $ivLength = false,
        public readonly bool $cipherText = false,
        public readonly bool $decryptionFailedBase64Decode = false,
        public readonly bool $decryptionFailedOpenSsl = false,
    ) {
        parent::__construct($message);
    }

    public static function masterKeyLength(): self
    {
        return new self(
            'The master key length must be 64 characters',
            true
        );
    }

    public static function ivLength(): self
    {
        return new self(
            'Iv length error. Expected 16 bytes, got false',
            false,
            true
        );
    }

    public static function cipherText(): self
    {
        return new self(
            'Cipher text error.',
            false,
            false,
            true
        );
    }

    public static function decryptionFailedBase64Decode(): self
    {
        return new self(
            'Base 64 decode failed.',
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
            return 'secret.exceptions.SR0003.encryption_error_master_key_length';
        }

        if (true === $this->ivLength) {
            return 'secret.exceptions.SR0004.encryption_error_iv_length';
        }

        if (true === $this->cipherText) {
            return 'secret.exceptions.SR0005.encryption_error_cipher_text';
        }

        if (true === $this->decryptionFailedBase64Decode) {
            return 'secret.exceptions.SR0006.encryption_error_decryption_failed_base64_decode';
        }

        return 'secret.exceptions.SR0007.encryption_error';
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

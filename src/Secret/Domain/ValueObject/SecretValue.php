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

namespace Marvin\Secret\Domain\ValueObject;

use EnderLab\DddCqrsBundle\Domain\Assert\Assert;
use Marvin\Secret\Domain\Service\EncryptionServiceInterface;
use Stringable;

final readonly class SecretValue implements Stringable
{
    public string $value;

    public function __construct(
        string $encryptedValue
    ) {
        Assert::notEmpty($encryptedValue, 'secret.exceptions.SR0018.secret_value_does_not_empty');
        $this->value = $encryptedValue;
    }

    public static function fromPlainText(
        string $plainText,
        EncryptionServiceInterface $encryption
    ): self {
        Assert::notEmpty($plainText, 'secret.exceptions.SR0018.secret_value_does_not_empty');
        $encrypted = $encryption->encrypt($plainText);
        return new self($encrypted);
    }

    public static function fromEncrypted(string $encryptedValue): self
    {
        Assert::notEmpty($encryptedValue, 'secret.exceptions.SR0018.secret_value_does_not_empty');

        return new self($encryptedValue);
    }

    public function decrypt(EncryptionServiceInterface $encryption): string
    {
        return $encryption->decrypt($this->value);
    }

    public function getEncrypted(): string
    {
        return $this->value;
    }

    public function __toString(): string
    {
        return '***ENCRYPTED***';
    }
}

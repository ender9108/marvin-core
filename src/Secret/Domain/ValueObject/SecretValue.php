<?php

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
        Assert::notEmpty($encryptedValue);
        $this->value = $encryptedValue;
    }

    public static function fromPlainText(
        string $plainText,
        EncryptionServiceInterface $encryption
    ): self {
        Assert::notEmpty($plainText);
        $encrypted = $encryption->encrypt($plainText);
        return new self($encrypted);
    }

    public static function fromEncrypted(string $encryptedValue): self
    {
        Assert::notEmpty($encryptedValue);

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

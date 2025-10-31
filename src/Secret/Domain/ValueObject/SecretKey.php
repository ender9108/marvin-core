<?php

namespace Marvin\Secret\Domain\ValueObject;

use EnderLab\DddCqrsBundle\Domain\Assert\Assert;
use Stringable;

final readonly class SecretKey implements Stringable
{
    public string $value;

    public function __construct(string $value)
    {
        Assert::notEmpty($value, 'secret.exceptions.SR0019.secret_key_does_not_empty');
        Assert::regex(
            $value,
            '/^[a-zA-Z0-9_.:-]{3,128}$/',
            'secret.exceptions.SR0020.secret_key_must_be_valid'
        );

        $this->value = $value;
    }

    public function __toString(): string
    {
        return $this->value;
    }
}

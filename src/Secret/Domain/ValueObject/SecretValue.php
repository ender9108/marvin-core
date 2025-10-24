<?php

namespace Marvin\Secret\Domain\ValueObject;

final readonly class SecretValue
{
    public string $value;

    public function __construct(string $value) {
        $this->value = $value;
    }
}

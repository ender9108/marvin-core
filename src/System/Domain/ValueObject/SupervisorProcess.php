<?php

namespace Marvin\System\Domain\ValueObject;

use Enderlab\DddCqrsBundle\Domain\Assert\Assert;
use Stringable;

final readonly class SupervisorProcess implements Stringable
{
    private string $value;

    public function __construct(string $value)
    {
        Assert::notEmpty($value, 'shared.exceptions.SY0012.supervisor_process_does_not_empty');
        Assert::regex($value, '/^[a-z0-9_\-]+$/i', 'shared.exceptions.SY0013.supervisor_process_is_not_valid');
        ;

        $this->value = $value;
    }

    public function __toString(): string
    {
        return $this->value;
    }
}

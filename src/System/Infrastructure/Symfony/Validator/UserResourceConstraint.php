<?php

namespace App\System\Infrastructure\Symfony\Validator;

use Attribute;
use Symfony\Component\Validator\Constraint;

#[Attribute]
class UserResourceConstraint extends Constraint
{
    public function getTargets(): string
    {
        return self::CLASS_CONSTRAINT;
    }
}

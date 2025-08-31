<?php

namespace App\System\Infrastructure\Symfony\Validator;

use Symfony\Component\Validator\Constraint;
use Symfony\Component\Validator\ConstraintValidator;

class UserResourceValidator extends ConstraintValidator
{

    /**
     * @inheritDoc
     */
    public function validate(mixed $value, Constraint $constraint)
    {
        // TODO: Implement validate() method.
    }
}

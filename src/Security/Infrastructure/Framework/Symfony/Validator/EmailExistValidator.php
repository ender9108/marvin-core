<?php

namespace Marvin\Security\Infrastructure\Framework\Symfony\Validator;

use Marvin\Security\Domain\Repository\UserRepositoryInterface;
use Symfony\Component\Validator\Constraint;
use Symfony\Component\Validator\ConstraintValidator;

class EmailExistValidator extends ConstraintValidator
{
    public function __construct(
        private UserRepositoryInterface $userRepository,
    ) {
    }

    public function validate(mixed $value, Constraint $constraint): void
    {
        if (null === $value || '' === $value) {
            return;
        }

        $exists = $this->userRepository->emailExists($value);

        if ($exists) {
            $this
                ->context
                ->buildViolation($constraint->message)
                ->setParameter('{{ value }}', $value)
                ->addViolation()
            ;
        }
    }
}

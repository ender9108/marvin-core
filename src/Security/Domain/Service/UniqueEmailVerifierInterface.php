<?php

namespace Marvin\Security\Domain\Service;

use Marvin\Shared\Domain\ValueObject\Email;

interface UniqueEmailVerifierInterface
{
    public function verify(Email $email): void;
}

<?php

namespace Marvin\Security\Presentation\Api\Dto\Input;

use Symfony\Component\Validator\Constraints\Email;
use Symfony\Component\Validator\Constraints\NotBlank;
use Symfony\Component\Validator\Constraints\PasswordStrength;

final class ResetPasswordUserDto
{
    #[NotBlank]
    public string $token;

    #[NotBlank]
    #[PasswordStrength]
    public string $password;
}

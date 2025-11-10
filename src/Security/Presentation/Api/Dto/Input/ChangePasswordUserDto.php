<?php

namespace Marvin\Security\Presentation\Api\Dto\Input;

use Symfony\Component\Validator\Constraints\NotBlank;
use Symfony\Component\Validator\Constraints\NotNull;
use Symfony\Component\Validator\Constraints\PasswordStrength;

final class ChangePasswordUserDto
{
    #[NotBlank]
    #[NotNull]
    public ?string $currentPassword = null;

    #[NotBlank]
    #[PasswordStrength]
    public ?string $newPassword = null;
}

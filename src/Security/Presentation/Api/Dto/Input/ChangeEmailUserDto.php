<?php

namespace Marvin\Security\Presentation\Api\Dto\Input;

use Symfony\Component\Validator\Constraints\Email;
use Symfony\Component\Validator\Constraints\NotBlank;

final class ChangeEmailUserDto
{
    #[NotBlank]
    #[Email]
    public string $email;
}

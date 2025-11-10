<?php

namespace Marvin\Security\Presentation\Api\Dto\Input;

use Marvin\Security\Infrastructure\Framework\Symfony\Validator\EmailExist;
use Marvin\Shared\Domain\Application;
use Symfony\Component\Validator\Constraints as Assert;

final class CreateUserDto
{
    #[Assert\Email]
    #[Assert\NotBlank]
    #[EmailExist]
    public string $email;

    #[Assert\NotBlank]
    public string $firstname;

    #[Assert\NotBlank]
    public string $lastname;

    #[Assert\NotBlank]
    public array $roles;

    #[Assert\Choice(choices: Application::APP_AVAILABLE_LOCALES)]
    public string $locale;

    #[Assert\Choice(choices: Application::APP_AVAILABLE_THEMES)]
    public string $theme;

    #[Assert\NotBlank]
    public string $timezone;

    #[Assert\NotBlank]
    #[Assert\PasswordStrength]
    public ?string $password = null;
}

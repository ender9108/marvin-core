<?php

namespace Marvin\Security\Presentation\Api\Dto\Input;

use Marvin\Shared\Domain\Application;
use Symfony\Component\Validator\Constraints\Choice;
use Symfony\Component\Validator\Constraints\Timezone;

final class UpdateProfileUserDto
{
    public ?string $firstname = null;

    public ?string $lastname = null;

    public array $roles = [];

    #[Choice(Application::APP_AVAILABLE_THEMES)]
    public ?string $theme = null;

    #[Choice(Application::APP_AVAILABLE_LOCALES)]
    public ?string $locale = null;

    #[Timezone]
    public ?string $timezone = null;
}

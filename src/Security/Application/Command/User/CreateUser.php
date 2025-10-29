<?php

namespace Marvin\Security\Application\Command\User;

use EnderLab\DddCqrsBundle\Application\Command\SyncCommandInterface;
use Marvin\Security\Domain\ValueObject\Firstname;
use Marvin\Security\Domain\ValueObject\Lastname;
use Marvin\Security\Domain\ValueObject\Roles;
use Marvin\Security\Domain\ValueObject\Timezone;
use Marvin\Security\Domain\ValueObject\UserType;
use Marvin\Shared\Domain\ValueObject\Email;
use Marvin\Shared\Domain\ValueObject\Locale;
use Marvin\Shared\Domain\ValueObject\Theme;

final readonly class CreateUser implements SyncCommandInterface
{
    public function __construct(
        public Email $email,
        public Firstname $firstname,
        public Lastname $lastname,
        public Roles $roles,
        public Locale $locale,
        public Theme $theme,
        public UserType $type,
        public Timezone $timezone,
        public string $password,
    ) {
    }
}

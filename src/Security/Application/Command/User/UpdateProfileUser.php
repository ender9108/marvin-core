<?php

namespace Marvin\Security\Application\Command\User;

use EnderLab\DddCqrsBundle\Application\Command\SyncCommandInterface;
use Marvin\Security\Domain\ValueObject\Firstname;
use Marvin\Security\Domain\ValueObject\Identity\UserId;
use Marvin\Security\Domain\ValueObject\Lastname;
use Marvin\Security\Domain\ValueObject\Roles;
use Marvin\Security\Domain\ValueObject\Timezone;
use Marvin\Shared\Domain\ValueObject\Locale;
use Marvin\Shared\Domain\ValueObject\Theme;

final readonly class UpdateProfileUser implements SyncCommandInterface
{
    public function __construct(
        public UserId $id,
        public ?Firstname $firstname = null,
        public ?Lastname $lastname = null,
        public ?Roles $roles = null,
        public ?Theme $theme = null,
        public ?Locale $locale = null,
        public ?Timezone $timezone = null,
    ) {
    }
}

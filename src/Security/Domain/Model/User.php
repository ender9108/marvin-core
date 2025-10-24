<?php

namespace Marvin\Security\Domain\Model;

use DateTimeImmutable;
use DateTimeInterface;
use EnderLab\DddCqrsBundle\Domain\Model\AggregateRoot;
use Marvin\Security\Domain\Event\User\UserDeleted;
use Marvin\Security\Domain\Exception\InvalidCurrentPassword;
use Marvin\Security\Domain\Exception\InvalidSamePassword;
use Marvin\Security\Domain\Service\PasswordHasherInterface;
use Marvin\Security\Domain\ValueObject\Firstname;
use Marvin\Security\Domain\ValueObject\Lastname;
use Marvin\Security\Domain\ValueObject\Roles;
use Marvin\Security\Domain\ValueObject\Timezone;
use Marvin\Security\Domain\ValueObject\UserStatus;
use Marvin\Security\Domain\ValueObject\UserType;
use Marvin\Shared\Domain\ValueObject\Email;
use Marvin\Shared\Domain\ValueObject\Identity\UserId;
use Marvin\Shared\Domain\ValueObject\Locale;
use Marvin\Shared\Domain\ValueObject\Theme;
use Marvin\Shared\Domain\ValueObject\UpdatedAt;

class User extends AggregateRoot
{
    public readonly UserId $id;

    public function __construct(
        private(set) Email $email,
        private(set) Firstname $firstname,
        private(set) Lastname $lastname,
        private(set) Roles $roles,
        private(set) Locale $locale,
        private(set) Theme $theme,
        private(set) UserStatus $status,
        private(set) UserType $type,
        private(set) Timezone $timezone,
        private(set) ?string $password = null,
        private(set) ?UpdatedAt $updatedAt = null,
        public readonly DateTimeInterface $createdAt = new DateTimeImmutable()
    ) {
        $this->id = new UserId();
    }

    public static function create(
        Email $email,
        Firstname $firstname,
        Lastname $lastname,
        UserStatus $status,
        UserType $type,
        Timezone $timezone,
        ?Roles $roles = null,
        ?Locale $locale = null,
        ?Theme $theme = null,
    ): self {
        return new self(
            $email,
            $firstname,
            $lastname,
            $roles ?? Roles::user(),
            $locale ?? Locale::fr(),
            $theme ?? Theme::dark(),
            $status,
            $type,
            $timezone
        );
    }

    public function changeEmail(Email $email): self
    {
        $this->email = $email;

        return $this;
    }

    public function updateProfile(
        ?Firstname $firstname = null,
        ?Lastname $lastname = null,
        ?Roles $roles = null,
        ?Theme $theme = null,
        ?Locale $locale = null,
        ?Timezone $timezone = null,
    ): self {
        $this->firstname = $firstname ?? $this->firstname;
        $this->lastname = $lastname ?? $this->lastname;
        $this->roles = $roles ?? $this->roles;
        $this->theme = $theme ?? $this->theme;
        $this->locale = $locale ?? $this->locale;
        $this->timezone = $timezone ?? $this->timezone;

        return $this;
    }

    public function enable(): self
    {
        $this->status = UserStatus::enabled();

        return $this;
    }

    public function disable(): self
    {
        $this->status = UserStatus::disabled();

        return $this;
    }

    public function delete(): self
    {
        $this->recordThat(new UserDeleted(
            $this->id,
            $this->type,
            $this->email
        ));

        return $this;
    }

    public function lock(): self
    {
        $this->status = UserStatus::locked();

        return $this;
    }

    public function definePassword(string $password, PasswordHasherInterface $passwordHasher): self
    {
        $this->password = $passwordHasher->hash($this, $password);

        return $this;
    }

    public function resetPassword(string $password, PasswordHasherInterface $passwordHasher): self
    {
        $this->password = $passwordHasher->hash($this, $password);

        return $this;
    }

    public function changePassword(string $currentPassword, string $newPassword, PasswordHasherInterface $passwordHasher): self
    {
        if ($this->password === null || !$passwordHasher->verify($this, $currentPassword)) {
            throw new InvalidCurrentPassword('Invalid current password.');
        }

        $newPasswordHash = $passwordHasher->hash($this, $newPassword);

        if ($newPasswordHash === $this->password) {
            throw new InvalidSamePassword('New password cannot be the same as the current one.');
        }

        $this->password = $newPasswordHash;

        return $this;
    }
}

<?php

namespace Marvin\Security\Domain\Model;

use DateTimeImmutable;
use EnderLab\DddCqrsBundle\Domain\Model\AggregateRoot;
use Marvin\Security\Domain\Event\User\UserCreated;
use Marvin\Security\Domain\Event\User\UserDeleted;
use Marvin\Security\Domain\Event\User\UserDisabled;
use Marvin\Security\Domain\Event\User\UserEmailUpdated;
use Marvin\Security\Domain\Event\User\UserEnabled;
use Marvin\Security\Domain\Event\User\UserLocked;
use Marvin\Security\Domain\Exception\InvalidCurrentPassword;
use Marvin\Security\Domain\Exception\InvalidSamePassword;
use Marvin\Security\Domain\Exception\InvalidUserStatus;
use Marvin\Security\Domain\List\UserStatusReference;
use Marvin\Security\Domain\Service\PasswordHasherInterface;
use Marvin\Security\Domain\ValueObject\Firstname;
use Marvin\Security\Domain\ValueObject\Identity\UserId;
use Marvin\Security\Domain\ValueObject\Lastname;
use Marvin\Security\Domain\ValueObject\Roles;
use Marvin\Shared\Domain\ValueObject\CreatedAt;
use Marvin\Shared\Domain\ValueObject\Email;
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
        private(set) ?string $password = null,
        private(set) ?UpdatedAt $updatedAt = null,
        public readonly CreatedAt $createdAt = new CreatedAt(new DateTimeImmutable())
    ) {
        $this->id = new UserId();
        //$this->recordThat(new UserCreated($this->id));
    }

    public static function create(
        Email $email,
        Firstname $firstname,
        Lastname $lastname,
        UserStatus $status,
        UserType $type,
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
            $type
        );
    }

    public function changeEmail(Email $email): self
    {
        $this->email = $email;
        //$this->recordThat(new UserEmailUpdated($this->id, $email));
        return $this;
    }

    public function updateProfile(
        Firstname $firstname,
        Lastname $lastname,
        Roles $roles,
        Theme $theme,
        Locale $locale,
    ): self {
        $this->firstname = $firstname;
        $this->lastname = $lastname;
        $this->roles = $roles;
        $this->theme = $theme;
        $this->locale = $locale;

        return $this;
    }

    public function enableUser(UserStatus $status): self
    {
        if ($status->reference->value !== UserStatusReference::STATUS_ENABLED->value) {
            throw InvalidUserStatus::withByActionAndReference(
                'enableUser',
                $status->reference->value
            );
        }

        $this->status = $status;
        //$this->recordThat(new UserEnabled($this->id));

        return $this;
    }

    public function disableUser(UserStatus $status): self
    {
        if ($status->reference->value !== UserStatusReference::STATUS_DISABLED->value) {
            throw InvalidUserStatus::withByActionAndReference(
                'disableUser',
                $status->reference->value
            );
        }

        $this->status = $status;
        //$this->recordThat(new UserDisabled($this->id));

        return $this;
    }

    public function delete(): self
    {
        $this->recordThat(new UserDeleted(
            $this->id->toString(),
            (string) $this->type->reference,
            (string) $this->email
        ));

        return $this;
    }

    public function lockUser(UserStatus $status): self
    {
        if ($status->reference->value !== UserStatusReference::STATUS_LOCKED->value) {
            throw InvalidUserStatus::withByActionAndReference(
                'lockUser',
                $status->reference->value
            );
        }

        $this->status = $status;
        //$this->recordThat(new UserLocked($this->id));

        return $this;
    }

    public function definePassword(string $password, PasswordHasherInterface $passwordHasher): self
    {
        $this->password = $passwordHasher->hash($this, $password);

        return $this;
    }

    public function updatePassword(string $currentPassword, string $newPassword, PasswordHasherInterface $passwordHasher): self
    {
        if ($this->password === null || !$passwordHasher->verify($this, $currentPassword)) {
            throw new InvalidCurrentPassword();
        }

        $newPasswordHash = $passwordHasher->hash($this, $newPassword);

        if ($newPasswordHash === $this->password) {
            throw new InvalidSamePassword();
        }

        $this->password = $newPasswordHash;

        return $this;
    }
}

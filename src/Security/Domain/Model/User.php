<?php
namespace Marvin\Security\Domain\Model;

use DateTimeImmutable;
use Marvin\Security\Domain\Event\User\UserCreated;
use Marvin\Security\Domain\Event\User\UserDeleted;
use Marvin\Security\Domain\Event\User\UserDisabled;
use Marvin\Security\Domain\Event\User\UserEmailUpdated;
use Marvin\Security\Domain\Event\User\UserEnabled;
use Marvin\Security\Domain\Event\User\UserLocked;
use Marvin\Security\Domain\Event\User\UserPasswordUpdated;
use Marvin\Security\Domain\Exception\InvalidCurrentPassword;
use Marvin\Security\Domain\Exception\InvalidUserStatus;
use Marvin\Security\Domain\Service\PasswordHasherInterface;
use Marvin\Security\Domain\ValueObject\Firstname;
use Marvin\Security\Domain\ValueObject\Identity\UserId;
use Marvin\Security\Domain\ValueObject\Lastname;
use Marvin\Security\Domain\ValueObject\Roles;
use Marvin\Shared\Domain\ValueObject\CreatedAt;
use Marvin\Shared\Domain\ValueObject\Email;
use EnderLab\DddCqrsBundle\Domain\Model\AggregateRoot;
use Marvin\Shared\Domain\ValueObject\UpdatedAt;

class User extends AggregateRoot
{
    public readonly UserId $id;

    public function __construct(
        private(set) Email $email,
        private(set) Firstname $firstName,
        private(set) Lastname $lastName,
        private(set) Roles $roles,
        private(set) UserStatus $status,
        private(set) UserType $type,
        private(set) ?string $password = null,
        private(set) ?UpdatedAt $updatedAt = null,
        public readonly CreatedAt $createdAt = new CreatedAt(new DateTimeImmutable())
    ) {
        $this->id = new UserId();
        $this->recordThat(new UserCreated($this->id));
    }

    public static function create(
        Email $email,
        Firstname $firstName,
        Lastname $lastName,
        UserStatus $status,
        UserType $type,
        ?Roles $roles
    ): self {
        return new self(
            $email,
            $firstName,
            $lastName,
            $roles ?? Roles::user(),
            $status,
            $type
        );
    }

    public function changeEmail(Email $email): self
    {
        $this->email = $email;
        $this->updatedAt = new UpdatedAt(new DateTimeImmutable());
        $this->recordThat(new UserEmailUpdated($this->id, $email));
        return $this;
    }

    public function updateProfile(
        Firstname $firstname,
        Lastname $lastName,
        Roles $roles
    ): self {
        $this->firstName = $firstname;
        $this->lastName = $lastName;
        $this->roles = $roles;
        $this->updatedAt = new UpdatedAt(new DateTimeImmutable());

        return $this;
    }

    public function enableUser(UserStatus $status): self
    {
        if ($status->reference->reference !== UserStatus::STATUS_ENABLED) {
            throw InvalidUserStatus::withByActionAndReference(
                'enableUser',
                $status->reference->reference
            );
        }

        $this->status = $status;
        $this->updatedAt = new UpdatedAt(new DateTimeImmutable());
        $this->recordThat(new UserEnabled($this->id));

        return $this;
    }

    public function disableUser(UserStatus $status): self
    {
        if ($status->reference->reference !== UserStatus::STATUS_DISABLED) {
            throw InvalidUserStatus::withByActionAndReference(
                'disableUser',
                $status->reference->reference
            );
        }

        $this->status = $status;
        $this->updatedAt = new UpdatedAt(new DateTimeImmutable());
        $this->recordThat(new UserDisabled($this->id));

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
        if ($status->reference->reference !== UserStatus::STATUS_LOCKED) {
            throw InvalidUserStatus::withByActionAndReference(
                'lockUser',
                $status->reference->reference
            );
        }

        $this->status = $status;
        $this->updatedAt = new UpdatedAt(new DateTimeImmutable());
        $this->recordThat(new UserLocked($this->id));

        return $this;
    }

    public function definePassword(string $password, PasswordHasherInterface $passwordHasher): self
    {
        $this->password = $passwordHasher->hash($this, $password);
        $this->updatedAt = new UpdatedAt(new DateTimeImmutable());

        return $this;
    }

    public function updatePassword(string $currentPassword, string $newPassword, PasswordHasherInterface $passwordHasher): self
    {
        if ($this->password === null || ! $passwordHasher->verify($this, $currentPassword)) {
            throw new InvalidCurrentPassword();
        }

        $this->password = $passwordHasher->hash($this, $newPassword);
        $this->recordThat(new UserPasswordUpdated($this->id, $newPassword));

        return $this;
    }
}

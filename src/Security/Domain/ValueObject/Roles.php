<?php
namespace Marvin\Security\Domain\ValueObject;

use EnderLab\DddCqrsBundle\Domain\Assert;
use Override;
use Stringable;

readonly class Roles implements Stringable
{
    private array $roles;

    public function __construct(array $roles = [Role::USER])
    {
        Assert::notEmpty($roles);
        Assert::allIsInstanceOf($roles, Role::class);

        $roles[] = Role::USER;
        $this->roles = array_unique(\array_map(fn (Role $role) => $role->value, $roles));
    }

    #[Override]
    public function __toString(): string
    {
        return implode(',', $this->roles);
    }

    public static function admin(): self
    {
        return new self([Role::USER, Role::ADMIN]);
    }

    public static function superAdmin(): self
    {
        return new self([Role::USER, Role::SUPER_ADMIN]);
    }

    public static function user(): self
    {
        return new self();
    }

    public function toArray(): array
    {
        return $this->roles;
    }

    public static function fromArray(array $roles): self
    {
        return new self($roles);
    }

    public function contains(string $role): bool
    {
        return in_array($role, $this->roles, true);
    }

    public function isUser(): bool
    {
        return count($this->roles) === 1 && $this->contains(Role::USER->value);
    }

    public function isAdmin(): bool
    {
        return $this->contains(Role::ADMIN->value);
    }

    public function isSuperAdmin(): bool
    {
        return $this->contains(Role::SUPER_ADMIN->value);
    }
}

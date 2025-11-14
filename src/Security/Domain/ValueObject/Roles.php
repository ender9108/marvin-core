<?php
/**
 * Marvin Core - DDD-based home automation system
 *
 * @package   Marvin\Core
 * @author    Alexandre Berthelot <alexandreberthelot9108@gmail.com>
 * @copyright 2024-present Alexandre Berthelot
 * @license   AGPL-3.0 License
 * @link      https://github.com/ender9108/marvin-core
 */

declare(strict_types=1);

namespace Marvin\Security\Domain\ValueObject;

use EnderLab\DddCqrsBundle\Domain\Assert\Assert;
use EnderLab\DddCqrsBundle\Domain\ValueObject\ValueObjectTrait;
use Override;
use Stringable;

final readonly class Roles implements Stringable
{
    use ValueObjectTrait;

    private array $value;

    public function __construct(array $roles = [])
    {
        if (!empty($roles)) {
            Assert::allIsInstanceOf($roles, Role::class);
        }

        $roles[] = Role::USER;
        $this->value = array_unique(\array_map(fn (Role $role) => $role->value, $roles));
    }

    #[Override]
    public function __toString(): string
    {
        return implode(',', $this->value);
    }

    public static function admin(): self
    {
        return new self([Role::ADMIN]);
    }

    public static function superAdmin(): self
    {
        return new self([Role::SUPER_ADMIN]);
    }

    public static function user(): self
    {
        return new self();
    }

    public function toArray(): array
    {
        $results = [];

        foreach ($this->value as $role) {
            $results[] = $role;
        }

        return $results;
    }

    public static function fromArray(array $roles): self
    {
        $enumRoles = [];

        foreach ($roles as $role) {
            $enumRoles[] = Role::from($role);
        }

        return new self($enumRoles);
    }

    public function contains(string $role): bool
    {
        return in_array($role, $this->value, true);
    }

    public function isUser(): bool
    {
        return count($this->value) === 1 && $this->contains(Role::USER->value);
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

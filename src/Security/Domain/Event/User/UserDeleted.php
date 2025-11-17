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

namespace Marvin\Security\Domain\Event\User;

use EnderLab\DddCqrsBundle\Domain\Event\AbstractDomainEvent;
use EnderLab\DddCqrsBundle\Domain\Event\DomainEventInterface;
use Marvin\Security\Domain\ValueObject\UserType;
use Marvin\Shared\Domain\ValueObject\Email;
use Marvin\Shared\Domain\ValueObject\Identity\UserId;

final readonly class UserDeleted extends AbstractDomainEvent implements DomainEventInterface
{
    public function __construct(
        public UserId $id,
        public UserType $type,
        public Email $email
    ) {
        parent::__construct();
    }

    public function getEventName(): string
    {
        return '$.security.user.deleted';
    }

    public function toArray(): array
    {
        return [
            'user_id' => $this->id->toString(),
            'type' => $this->type->value,
            'email' => (string) $this->email,
        ];
    }
}

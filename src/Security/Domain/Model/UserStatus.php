<?php

namespace Marvin\Security\Domain\Model;

use Marvin\Security\Domain\ValueObject\Identity\UserStatusId;
use Marvin\Shared\Domain\ValueObject\CreatedAt;
use Marvin\Shared\Domain\ValueObject\Label;
use Marvin\Shared\Domain\ValueObject\Reference;
use DateTimeImmutable;

class UserStatus
{
    public const string STATUS_ENABLED = 'enabled';
    public const string STATUS_DISABLED = 'disabled';
    public const string STATUS_TO_DELETE = 'to_delete';
    public const string STATUS_LOCKED = 'locked';

    private(set) UserStatusId $id;

    public function __construct(
        public readonly Label $label,
        public readonly Reference $reference,
        public readonly CreatedAt $createdAt = new CreatedAt(new DateTimeImmutable())
    ) {
        $this->id = new UserStatusId();
    }
}

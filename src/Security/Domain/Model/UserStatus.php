<?php

namespace Marvin\Security\Domain\Model;

use DateTimeImmutable;
use Marvin\Security\Domain\ValueObject\Identity\UserStatusId;
use Marvin\Shared\Domain\ValueObject\CreatedAt;
use Marvin\Shared\Domain\ValueObject\Label;
use Marvin\Shared\Domain\ValueObject\Reference;

class UserStatus
{
    public private(set) UserStatusId $id;

    public function __construct(
        public readonly Label $label,
        public readonly Reference $reference,
        public readonly CreatedAt $createdAt = new CreatedAt(new DateTimeImmutable())
    ) {
        $this->id = new UserStatusId();
    }
}

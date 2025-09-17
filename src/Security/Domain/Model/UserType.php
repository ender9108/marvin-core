<?php

namespace Marvin\Security\Domain\Model;

use DateTimeImmutable;
use Marvin\Security\Domain\ValueObject\Identity\UserTypeId;
use Marvin\Shared\Domain\ValueObject\CreatedAt;
use Marvin\Shared\Domain\ValueObject\Label;
use Marvin\Shared\Domain\ValueObject\Reference;

class UserType
{
    public private(set) UserTypeId $id;

    public function __construct(
        private(set) Label $label,
        private(set) Reference $reference,
        private(set) CreatedAt $createdAt = new CreatedAt(new DateTimeImmutable())
    ) {
        $this->id = new UserTypeId();
    }
}

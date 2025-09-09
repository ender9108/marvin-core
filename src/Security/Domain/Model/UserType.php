<?php

namespace Marvin\Security\Domain\Model;

use Marvin\Security\Domain\ValueObject\Identity\UserTypeId;
use Marvin\Shared\Domain\ValueObject\CreatedAt;
use Marvin\Shared\Domain\ValueObject\Label;
use Marvin\Shared\Domain\ValueObject\Reference;
use DateTimeImmutable;

class UserType
{
    private(set) UserTypeId $id;

    public function __construct(
        private(set) Label $label,
        private(set) Reference $reference,
        private(set) CreatedAt $createdAt = new CreatedAt(new DateTimeImmutable())
    ) {
        $this->id = new UserTypeId();
    }
}

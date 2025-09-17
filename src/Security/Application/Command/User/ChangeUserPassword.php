<?php

namespace Marvin\Security\Application\Command\User;

use EnderLab\DddCqrsBundle\Application\Command\SyncCommandInterface;
use Marvin\Security\Domain\ValueObject\Identity\UserId;

final readonly class ChangeUserPassword implements SyncCommandInterface
{
    public function __construct(
        public UserId $id,
        public string $currentPassword,
        public string $newPassword,
    ) {
    }
}

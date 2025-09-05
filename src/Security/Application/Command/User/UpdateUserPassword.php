<?php
namespace Marvin\Security\Application\Command\User;

use Marvin\Security\Domain\ValueObject\Identity\UserId;
use EnderLab\DddCqrsBundle\Application\Command\SyncCommandInterface;

final readonly class UpdateUserPassword implements SyncCommandInterface
{
    public function __construct(
        public UserId $id,
        public string $currentPassword,
        public string $newPassword,
    ) {
    }
}

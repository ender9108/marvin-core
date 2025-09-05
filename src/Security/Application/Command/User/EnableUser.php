<?php
namespace Marvin\Security\Application\Command\User;

use Marvin\Security\Domain\ValueObject\Identity\UserId;
use EnderLab\DddCqrsBundle\Application\Command\SyncCommandInterface;

final readonly class EnableUser implements SyncCommandInterface
{
    public function __construct(
        public UserId $id,
    ) {
    }
}

<?php

namespace Marvin\Security\Application\Command\User;

use EnderLab\DddCqrsBundle\Application\Command\SyncCommandInterface;
use Marvin\Shared\Domain\ValueObject\Email;

final readonly class RequestResetPasswordUser implements SyncCommandInterface
{
    public function __construct(
        public Email $email,
    ) {
    }
}

<?php

namespace Marvin\Security\Domain\Service;

use Marvin\Security\Domain\Exception\EmailAlreadyUsed;
use Marvin\Security\Domain\Repository\UserRepositoryInterface;
use Marvin\Shared\Domain\ValueObject\Email;

final readonly class UniqueEmailVerifier
{
    public function __construct(
        private UserRepositoryInterface $userRepository
    ) {
    }

    public function verify(Email $email): void
    {
        $used = $this->userRepository->byEmail($email);

        if ($used !== null) {
            throw EmailAlreadyUsed::withEmail($email);
        }
    }
}

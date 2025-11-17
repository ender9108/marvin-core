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

namespace Marvin\Security\Infrastructure\Framework\Symfony\Service;

use Marvin\Security\Domain\Exception\EmailAlreadyUsed;
use Marvin\Security\Domain\Repository\UserRepositoryInterface;
use Marvin\Security\Domain\Service\UniqueEmailVerifierInterface;
use Marvin\Shared\Domain\ValueObject\Email;

final readonly class UniqueEmailVerifier implements UniqueEmailVerifierInterface
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

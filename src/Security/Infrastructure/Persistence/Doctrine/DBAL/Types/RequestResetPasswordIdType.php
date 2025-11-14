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

namespace Marvin\Security\Infrastructure\Persistence\Doctrine\DBAL\Types;

use Marvin\Security\Domain\ValueObject\Identity\RequestResetPasswordId as RequestResetPasswordId;
use Override;
use Symfony\Bridge\Doctrine\Types\AbstractUidType;

final class RequestResetPasswordIdType extends AbstractUidType
{
    #[Override]
    public function getName(): string
    {
        return 'request_reset_password_id';
    }

    #[Override]
    protected function getUidClass(): string
    {
        return RequestResetPasswordId::class;
    }
}

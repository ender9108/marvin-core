<?php

namespace Marvin\Shared\Infrastructure\Persistence\Doctrine\DBAL\Types;

use Marvin\Shared\Domain\ValueObject\Identity\DeviceId;
use Override;
use Symfony\Bridge\Doctrine\Types\AbstractUidType;

final class DeviceIdType extends AbstractUidType
{
    #[Override]
    public function getName(): string
    {
        return 'device_id';
    }

    #[Override]
    protected function getUidClass(): string
    {
        return DeviceId::class;
    }
}

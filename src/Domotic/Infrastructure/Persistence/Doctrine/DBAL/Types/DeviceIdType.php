<?php

namespace Marvin\Domotic\Infrastructure\Persistence\Doctrine\DBAL\Types;

use Symfony\Bridge\Doctrine\Types\AbstractUidType;
use Marvin\Domotic\Domain\ValueObject\Identity\DeviceId;

final class DeviceIdType extends AbstractUidType
{
    #[\Override]
    public function getName(): string
    {
        return 'device_id';
    }

    #[\Override]
    protected function getUidClass(): string
    {
        return DeviceId::class;
    }
}

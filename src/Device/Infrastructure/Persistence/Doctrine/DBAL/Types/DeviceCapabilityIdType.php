<?php

namespace Marvin\Device\Infrastructure\Persistence\Doctrine\DBAL\Types;

use Marvin\Device\Domain\ValueObject\Identity\DeviceCapabilityId;
use Override;
use Symfony\Bridge\Doctrine\Types\AbstractUidType;

final class DeviceCapabilityIdType extends AbstractUidType
{
    #[Override]
    public function getName(): string
    {
        return 'device_capability_id';
    }

    #[Override]
    protected function getUidClass(): string
    {
        return DeviceCapabilityId::class;
    }
}

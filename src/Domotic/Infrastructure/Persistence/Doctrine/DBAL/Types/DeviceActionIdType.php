<?php

namespace Marvin\Domotic\Infrastructure\Persistence\Doctrine\DBAL\Types;

use Marvin\Domotic\Domain\ValueObject\Identity\DeviceActionId;
use Symfony\Bridge\Doctrine\Types\AbstractUidType;

final class DeviceActionIdType extends AbstractUidType
{
    #[\Override]
    public function getName(): string
    {
        return 'device_action_id';
    }

    #[\Override]
    protected function getUidClass(): string
    {
        return DeviceActionId::class;
    }
}

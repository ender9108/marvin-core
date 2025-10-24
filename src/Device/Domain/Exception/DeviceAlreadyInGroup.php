<?php

namespace Marvin\Device\Domain\Exception;

use EnderLab\DddCqrsBundle\Domain\Exception\DomainException;
use EnderLab\DddCqrsBundle\Domain\Exception\TranslatableExceptionInterface;
use Marvin\Device\Domain\Model\Device;

final class DeviceAlreadyInGroup extends DomainException implements TranslatableExceptionInterface
{
    public function __construct(
        string $message,
        string $code,
        public readonly array $infos = []
    ) {
        parent::__construct($message, $code);
    }

    public static function withDevice(Device $device, Device $existingGroup): self
    {
        return new self(
            sprintf(
                'Device "%s" (ID: %s) is already member of group "%s" (ID: %s)',
                $device->label->value,
                $device->id->toString(),
                $existingGroup->label->value,
                $existingGroup->id->toString(),
            ),
            'D00010',
            [
                [
                    'deviceName' => $device->label->value,
                    'deviceId' => $device->id->toString(),
                    'groupName' => $existingGroup->label->value,
                    'groupId' => $existingGroup->id->toString()
                ]
            ]
        );
    }

    public static function withDevices(array $devices, array $existingGroups): self
    {
        $deviceNames = array_map(
            fn (Device $d) => $d->label->value,
            $devices
        );
        $groupNames = array_map(
            fn (Device $d) => $d->label->value,
            $devices
        );

        $infos = array_map(
            fn (Device $device) => [
                'deviceName' => $device->label->value,
                'deviceId' => $device->id->toString(),
                'groupName' => '@todo',
                'groupId' => '@todo'
            ],
            $devices
        );

        return new self(
            sprintf(
                'Devices [%s] are already in groups and cannot be added',
                implode(', ', $deviceNames),
            ),
            'D00010',
            $infos
        );
    }

    public function translationId(): string
    {
        return 'device.exceptions.device_already_in_group';
    }

    public function translationParameters(): array
    {
        $deviceNames = array_map(
            fn (array $deviceInfos) => $deviceInfos['deviceName'] ?? 'unknown',
            $this->infos
        );
        $deviceIds = array_map(
            fn (array $deviceInfos) => $deviceInfos['deviceId'] ?? 'unknown',
            $this->infos
        );

        return [
            '%device_name%' => implode(', ', $deviceNames),
            '%device_ids%' => implode(', ', $deviceIds),
            '%group_name%' => $this->infos[0]['groupName'] ?? 'unknown',
            '%group_id%' => $this->infos[0]['groupId'] ?? 'unknown',
        ];
    }

    public function translationDomain(): string
    {
        return 'device';
    }
}

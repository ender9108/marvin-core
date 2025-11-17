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

namespace Marvin\Device\Infrastructure\Persistence\Doctrine\ORM\Repository;

use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;
use Marvin\Device\Domain\Exception\DeviceNotFound;
use Marvin\Device\Domain\Model\Device;
use Marvin\Device\Domain\Repository\DeviceRepositoryInterface;
use Marvin\Device\Domain\ValueObject\Capability;
use Marvin\Device\Domain\ValueObject\CompositeType;
use Marvin\Device\Domain\ValueObject\DeviceType;
use Marvin\Device\Domain\ValueObject\PhysicalAddress;
use Marvin\Device\Domain\ValueObject\Protocol;
use Marvin\Device\Domain\ValueObject\TechnicalName;
use Marvin\Shared\Domain\ValueObject\Identity\DeviceId;
use Marvin\Shared\Domain\ValueObject\Identity\ProtocolId;
use Marvin\Shared\Domain\ValueObject\Identity\ZoneId;

/**
 * Doctrine ORM implementation of DeviceRepositoryInterface
 */
final class DoctrineDeviceRepository extends ServiceEntityRepository implements DeviceRepositoryInterface
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Device::class);
    }

    // Interface aliases - map interface methods to internal implementation methods
    public function all(): array
    {
        return $this->createQueryBuilder('d')
            ->orderBy('d.label.value', 'ASC')
            ->getQuery()
            ->getResult()
        ;
    }

    public function byId(DeviceId $deviceId, bool $throwOnNotFound = true): Device
    {
        /** @var Device $device */
        $device = $this->find($deviceId);

        if ($device === null && $throwOnNotFound) {
            throw DeviceNotFound::withId($deviceId);
        }

        return $device;
    }

    public function byProtocol(Protocol $protocol, ?ProtocolId $protocolId = null): array
    {
        $query = $this
            ->createQueryBuilder('d')
            ->where('d.protocol = :protocol')
            ->setParameter('protocol', $protocol->value)
            ->orderBy('d.label.value', 'ASC')
        ;

        if ($protocolId !== null) {
            $query
                ->andWhere('d.protocolId = :protocolId')
                ->setParameter('protocolId', $protocolId->toString())
            ;
        }

        return $query->getQuery()->getResult();
    }

    public function byZone(ZoneId $zoneId): array
    {
        return $this
            ->createQueryBuilder('d')
            ->where('d.zoneId = :zoneId')
            ->setParameter('zoneId', $zoneId->toString())
            ->orderBy('d.label.value', 'ASC')
            ->getQuery()
            ->getResult()
        ;
    }

    public function byCapability(Capability $capability): array
    {
        return $this
            ->createQueryBuilder('d')
            ->join('d.capabilities', 'c')
            ->where('c.capability = :capability')
            ->setParameter('capability', $capability->value)
            ->orderBy('d.label.value', 'ASC')
            ->getQuery()
            ->getResult()
        ;
    }

    public function byType(DeviceType $deviceType): array
    {
        return $this
            ->createQueryBuilder('d')
            ->where('d.deviceType = :deviceType')
            ->setParameter('deviceType', $deviceType->value)
            ->orderBy('d.label.value', 'ASC')
            ->getQuery()
            ->getResult()
        ;
    }

    public function getCompositeDevices(?CompositeType $compositeType = null): array
    {
        $query = $this
            ->createQueryBuilder('d')
            ->where('d.deviceType = :deviceType')
            ->setParameter('deviceType', DeviceType::COMPOSITE->value)
            ->orderBy('d.label.value', 'ASC')
        ;

        if ($compositeType !== null) {
            $query->andWhere('d.compositeType = :compositeType')
                ->setParameter('compositeType', $compositeType->value);
        }

        return $query->getQuery()->getResult();
    }

    public function byPhysicalAddress(PhysicalAddress $physicalAddress, Protocol $protocol): ?Device
    {
        return $this
            ->createQueryBuilder('d')
            ->where('d.physicalAddress.value = :physicalAddress')
            ->andWhere('d.protocol = :protocol')
            ->setParameter('physicalAddress', $physicalAddress->value)
            ->setParameter('protocol', $protocol->value)
            ->getQuery()
            ->getOneOrNullResult()
        ;
    }

    public function byTechnicalName(TechnicalName $technicalName, ProtocolId $protocolId): ?Device
    {
        return $this
            ->createQueryBuilder('d')
            ->where('d.technicalName.value = :technicalName')
            ->andWhere('d.protocolId = :protocolId')
            ->setParameter('technicalName', $technicalName->value)
            ->setParameter('protocolId', $protocolId->toString())
            ->getQuery()
            ->getOneOrNullResult()
        ;
    }

    public function byChildDeviceId(DeviceId $childDeviceId): array
    {
        return $this
            ->createQueryBuilder('d')
            ->where('d.deviceType = :deviceType')
            ->andWhere('JSON_CONTAINS(d.childDeviceIds, :childDeviceId) = 1')
            ->setParameter('deviceType', DeviceType::COMPOSITE->value)
            ->setParameter('childDeviceId', json_encode($childDeviceId->toString()))
            ->getQuery()
            ->getResult()
        ;
    }

    public function byDevicesById(array $deviceIds): array
    {
        if (empty($deviceIds)) {
            return [];
        }

        $deviceIdStrings = array_map(fn (DeviceId $id) => $id->toString(), $deviceIds);

        $devices = $this
            ->createQueryBuilder('d')
            ->where('d.deviceId IN (:deviceIds)')
            ->setParameter('deviceIds', $deviceIdStrings)
            ->getQuery()
            ->getResult()
        ;

        // Index by device ID string for easy lookup
        $indexed = [];

        foreach ($devices as $device) {
            $indexed[$device->id()->toString()] = $device;
        }

        return $indexed;
    }

    public function byNativeGroupId(string $nativeGroupId, ProtocolId $protocolId): ?Device
    {
        return $this
            ->createQueryBuilder('d')
            ->where('d.nativeGroupInfo.protocolGroupId = :nativeGroupId')
            ->andWhere('d.nativeGroupInfo.protocolId = :protocolId')
            ->setParameter('nativeGroupId', $nativeGroupId)
            ->setParameter('protocolId', $protocolId->toString())
            ->getQuery()
            ->getOneOrNullResult()
        ;
    }

    public function byNativeSceneId(string $nativeSceneId, ProtocolId $protocolId): ?Device
    {
        return $this
            ->createQueryBuilder('d')
            ->where('d.nativeSceneInfo.protocolSceneId = :nativeSceneId')
            ->andWhere('d.nativeSceneInfo.protocolId = :protocolId')
            ->setParameter('nativeSceneId', $nativeSceneId)
            ->setParameter('protocolId', $protocolId->toString())
            ->getQuery()
            ->getOneOrNullResult()
        ;
    }

    public function save(Device $device, bool $flush = true): void
    {
        $this->getEntityManager()->persist($device);

        if ($flush) {
            $this->getEntityManager()->flush();
        }
    }

    public function remove(Device $device, bool $flush = true): void
    {
        $this->getEntityManager()->remove($device);

        if ($flush) {
            $this->getEntityManager()->flush();
        }
    }

    public function exists(DeviceId $deviceId): bool
    {
        return $this->byId($deviceId, false) !== null;
    }

    public function countDevices(?Protocol $protocol = null, ?ZoneId $zoneId = null): int
    {
        $query = $this
            ->createQueryBuilder('d')
            ->select('COUNT(d.deviceId)')
        ;

        if ($protocol !== null) {
            $query
                ->where('d.protocol = :protocol')
                ->setParameter('protocol', $protocol->value)
            ;
        }

        if ($zoneId !== null) {
            $query
                ->andWhere('d.zoneId = :zoneId')
                ->setParameter('zoneId', $zoneId->toString())
            ;
        }

        return (int) $query->getQuery()->getSingleScalarResult();
    }
}

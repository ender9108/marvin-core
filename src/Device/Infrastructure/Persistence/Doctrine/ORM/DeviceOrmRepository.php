<?php

namespace Marvin\Device\Infrastructure\Persistence\Doctrine\ORM;

use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;
use Marvin\Device\Domain\Exception\DeviceNotFound;
use Marvin\Device\Domain\Model\Device;
use Marvin\Device\Domain\Repository\DeviceRepositoryInterface;
use Marvin\Device\Domain\ValueObject\DeviceType;
use Marvin\Device\Domain\ValueObject\VirtualDeviceType;
use Marvin\Shared\Domain\ValueObject\Identity\DeviceId;
use Marvin\Shared\Domain\ValueObject\Identity\ProtocolId;
use Marvin\Shared\Domain\ValueObject\Identity\ZoneId;

/**
 * @extends ServiceEntityRepository<Device>
 */
class DeviceOrmRepository extends ServiceEntityRepository implements DeviceRepositoryInterface
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Device::class);
    }

    public function save(Device $model, bool $flush = true): void
    {
        $this->getEntityManager()->persist($model);

        if (true === $flush) {
            $this->getEntityManager()->flush();
        }
    }

    public function remove(Device $model, bool $flush = true): void
    {
        $this->getEntityManager()->remove($model);

        if (true === $flush) {
            $this->getEntityManager()->flush();
        }
    }

    public function byId(DeviceId $id): ?Device
    {
        $device = $this->find($id->toString());

        if (null === $device) {
            throw DeviceNotFound::withId($id);
        }

        return $device;
    }

    public function byIds(array $ids): array
    {
        if (empty($deviceIds)) {
            return [];
        }

        // Convertir les strings en DeviceId
        $deviceIdObjects = array_map(
            DeviceId::fromString(...),
            $deviceIds
        );

        $qb = $this->createQueryBuilder('d');
        $qb
            ->where($qb->expr()->in('d.id', ':deviceIds'))
            ->setParameter('deviceIds', $deviceIdObjects)
        ;

        return $qb->getQuery()->getResult();
    }

    public function byLabel(string $label): ?Device
    {
        return $this->createQueryBuilder('d')
            ->where('d.label.value = :label')
            ->setParameter('label', $label)
            ->getQuery()
            ->getOneOrNullResult()
        ;
    }

    public function byPhysicalAddress(string $physicalAddress): ?Device
    {
        return $this->createQueryBuilder('d')
            ->where('d.physicalAddress = :address')
            ->andWhere('d.type = :type')
            ->setParameter('address', $physicalAddress)
            ->setParameter('type', DeviceType::PHYSICAL->value)
            ->getQuery()
            ->getOneOrNullResult();
    }

    public function byProtocolId(ProtocolId $protocolId): array
    {
        return $this->createQueryBuilder('d')
            ->where('d.protocolId.value = :protocolId')
            ->setParameter('protocolId', $protocolId->toString())
            ->getQuery()
            ->getResult();
    }

    public function byZoneId(ZoneId $zoneId): array
    {
        return $this->createQueryBuilder('d')
            ->where('d.zoneId.value = :zoneId')
            ->setParameter('zoneId', $zoneId->toString())
            ->getQuery()
            ->getResult();
    }

    public function byType(DeviceType $type): array
    {
        return $this->createQueryBuilder('d')
            ->where('d.type = :type')
            ->setParameter('type', $type->value)
            ->getQuery()
            ->getResult();
    }

    public function getVirtualByType(VirtualDeviceType $virtualType): array
    {
        return $this->createQueryBuilder('d')
            ->where('d.type = :type')
            ->andWhere('d.virtualType = :virtualType')
            ->setParameter('type', DeviceType::VIRTUAL->value)
            ->setParameter('virtualType', $virtualType->value)
            ->getQuery()
            ->getResult();
    }

    public function all(): array
    {
        return $this->createQueryBuilder('d')
            ->orderBy('d.name.value', 'ASC')
            ->getQuery()
            ->getResult();
    }

    public function getComposites(): array
    {
        return $this
            ->createQueryBuilder('d')
            ->where('d.type = :type')
            ->setParameter('type', DeviceType::COMPOSITE)
            ->getQuery()
            ->getResult()
        ;
    }

    public function byCompositeId(DeviceId $compositeId): array
    {
        // Récupérer le composite
        $composite = $this->find($compositeId);

        if (!$composite || $composite->type !== DeviceType::COMPOSITE) {
            return [];
        }

        // Récupérer ses children
        $childIds = array_map(
            fn (DeviceId $id) => $id->toString(),
            $composite->getChildDeviceIds()
        );

        return $this->byIds($childIds);
    }

    public function getGroups(): array
    {
        return $this->createQueryBuilder('d')
            ->where('d.type = :type')
            ->setParameter('type', DeviceType::COMPOSITE)
            ->andWhere('d.sceneStates IS NOT NULL')
            ->andWhere('d.sceneStates != :emptyJson')
            ->setParameter('emptyJson', '{}')
            ->orderBy('d.name.value', 'ASC')
            ->getQuery()
            ->getResult();
    }

    public function getGroupById(DeviceId $groupId): Device
    {
        $group = $this->createQueryBuilder('d')
            ->where('d.type = :type')
            ->setParameter('type', DeviceType::COMPOSITE)
            ->andWhere('d.sceneStates IS NOT NULL')
            ->andWhere('d.sceneStates != :emptyJson')
            ->setParameter('emptyJson', '{}')
            ->andWhere('d.id = :groupId')
            ->setParameter('groupId', $groupId)
            ->getQuery()
            ->getOneOrNullResult();

        if (null === $group) {
            throw DeviceNotFound::withId($groupId);
        }

        return $group;
    }

    public function getScenes(): array
    {
        return $this
            ->createQueryBuilder('d')
            ->where('d.type = :type')
            ->andWhere('d.sceneStates IS NOT NULL')
            ->andWhere('d.sceneStates != :emptyJson')
            ->setParameter('type', DeviceType::COMPOSITE)
            ->setParameter('emptyJson', '{}')
            ->getQuery()
            ->getResult()
        ;
    }

    public function getSceneById(DeviceId $sceneId): Device
    {
        $scene = $this
            ->createQueryBuilder('d')
            ->where('d.type = :type')
            ->andWhere('d.sceneStates IS NOT NULL')
            ->andWhere('d.sceneStates != :emptyJson')
            ->setParameter('type', DeviceType::COMPOSITE)
            ->setParameter('emptyJson', '{}')
            ->andWhere('d.id = :sceneId')
            ->setParameter('sceneId', $sceneId)
            ->getQuery()
            ->getOneOrNullResult();

        if (null === $scene) {
            throw DeviceNotFound::withId($sceneId);
        }

        return $scene;
    }

    public function getCompositesByChildDevice(DeviceId $childDeviceId): array
    {
        return $this
            ->createQueryBuilder('d')
            ->where('d.type = :type')
            ->andWhere("JSON_CONTAINS(d.childDeviceIds, :deviceId, '$') = 1")
            ->setParameter('type', DeviceType::COMPOSITE)
            ->setParameter('deviceId', json_encode($childDeviceId->toString()))
            ->getQuery()
            ->getResult()
        ;
    }

    public function getCompositesByZone(ZoneId $zoneId): array
    {
        return $this
            ->createQueryBuilder('d')
            ->where('d.type = :type')
            ->andWhere('d.zoneId = :zoneId')
            ->setParameter('type', DeviceType::COMPOSITE)
            ->setParameter('zoneId', $zoneId)
            ->getQuery()
            ->getResult();
    }
}

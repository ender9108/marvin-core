<?php

namespace Marvin\Device\Infrastructure\Persistence\Doctrine\ORM;

use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;
use Marvin\Device\Domain\Exception\DeviceNotFound;
use Marvin\Device\Domain\Model\Device;
use Marvin\Device\Domain\Model\DeviceCapability;
use Marvin\Device\Domain\Repository\DeviceCapabilityRepositoryInterface;
use Marvin\Shared\Domain\ValueObject\Identity\DeviceId;

/**
 * @extends ServiceEntityRepository<Device>
 */
class DeviceCapabilityOrmRepository extends ServiceEntityRepository implements DeviceCapabilityRepositoryInterface
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, DeviceCapability::class);
    }

    public function save(DeviceCapability $capability, bool $flush = true): void
    {
        $this->getEntityManager()->persist($capability);

        if (true === $flush) {
            $this->getEntityManager()->flush();
        }
    }

    public function remove(DeviceCapability $capability, bool $flush = true): void
    {
        $this->getEntityManager()->remove($capability);

        if (true === $flush) {
            $this->getEntityManager()->flush();
        }
    }

    public function all(): array
    {
        return $this
            ->createQueryBuilder('dc')
            ->addOrderBy('dc.name', 'ASC')
            ->getQuery()
            ->getResult()
        ;
    }

    public function byId(DeviceId $id): DeviceCapability
    {
        /** @var DeviceCapability $deviceCapability */
        $deviceCapability = $this->find($id->toString());

        if (null === $deviceCapability) {
            throw DeviceNotFound::withId($id);
        }

        return $deviceCapability;
    }

    public function byDeviceId(DeviceId $id): array
    {
        return $this
            ->createQueryBuilder('dc')
            ->where('dc.device.id = :deviceId')
            ->setParameter('deviceId', $id->toString())
            ->getQuery()
            ->getResult()
        ;
    }
}

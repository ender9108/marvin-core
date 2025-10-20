<?php

namespace Marvin\Device\Infrastructure\Persistence\Doctrine\ORM;

use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;
use Marvin\Device\Domain\Model\Device;
use Marvin\Device\Domain\Repository\DeviceRepositoryInterface;
use Marvin\Device\Domain\ValueObject\Identity\DeviceId;

/**
 * @extends ServiceEntityRepository<Device>
 */
class DeviceOrmRepository extends ServiceEntityRepository implements DeviceRepositoryInterface
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Device::class);
    }

    public function save(Device $model): void
    {
        $this->getEntityManager()->persist($model);
        $this->getEntityManager()->flush();
    }

    public function remove(Device $model): void
    {
        $this->getEntityManager()->remove($model);
        $this->getEntityManager()->flush();
    }

    public function byId(DeviceId $id): ?Device
    {
        return $this->find($id->toString());
    }

    public function getComposites(): array
    {
        // TODO: Implement getComposites() method.
    }

    public function getGroups(): array
    {
        // TODO: Implement getGroups() method.
    }

    public function getScenes(): array
    {
        // TODO: Implement getScenes() method.
    }

    public function getCompositesByChildDevice(DeviceId $childDeviceId): array
    {
        // TODO: Implement getCompositesByChildDevice() method.
    }

    public function getCompositesWithNativeSupport(): array
    {
        // TODO: Implement getCompositesWithNativeSupport() method.
    }
}

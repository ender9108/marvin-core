<?php

namespace App\Domotic\Infrastructure\Doctrine\Repository;

use App\Domotic\Domain\Model\Device;
use App\Domotic\Domain\Repository\DeviceRepositoryInterface;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

final class DoctrineDeviceRepository extends ServiceEntityRepository implements DeviceRepositoryInterface
{
    private const string ENTITY_CLASS = Device::class;

    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, self::ENTITY_CLASS);
    }

    public function add(Device $device): void
    {
        $this->getEntityManager()->persist($device);
    }

    public function remove(Device $device): void
    {
        $this->getEntityManager()->remove($device);
    }

    public function byId(string|int $id): ?Device
    {
        return $this->find($id);
    }
}

<?php

namespace App\Domotic\Infrastructure\Doctrine\Repository;

use App\Domotic\Domain\Model\ProtocolStatus;
use App\Domotic\Domain\Repository\ProtocolStatusRepositoryInterface;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

final class DoctrineProtocolStatusRepository extends ServiceEntityRepository implements ProtocolStatusRepositoryInterface
{
    private const string ENTITY_CLASS = ProtocolStatus::class;

    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, self::ENTITY_CLASS);
    }

    public function add(ProtocolStatus $protocolStatus): void
    {
        $this->getEntityManager()->persist($protocolStatus);
    }

    public function remove(ProtocolStatus $protocolStatus): void
    {
        $this->getEntityManager()->remove($protocolStatus);
    }

    public function byId(string|int $id): ?ProtocolStatus
    {
        return $this->find($id);
    }
}

<?php

namespace App\Domotic\Infrastructure\Doctrine\Repository;

use App\Domotic\Domain\Model\Protocol;
use App\Domotic\Domain\Repository\ProtocolRepositoryInterface;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

final class DoctrineProtocolRepository extends ServiceEntityRepository implements ProtocolRepositoryInterface
{
    private const string ENTITY_CLASS = Protocol::class;

    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, self::ENTITY_CLASS);
    }

    public function add(Protocol $protocol): void
    {
        $this->getEntityManager()->persist($protocol);
    }

    public function remove(Protocol $protocol): void
    {
        $this->getEntityManager()->remove($protocol);
    }

    public function byId(string|int $id): ?Protocol
    {
        return $this->find($id);
    }
}

<?php

namespace EnderLab\MarvinManagerBundle\Repository;

use EnderLab\MarvinManagerBundle\Entity\DockerCustomCommand;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @extends ServiceEntityRepository<DockerCustomCommand>
 */
class DockerCustomCommandRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, DockerCustomCommand::class);
    }
}

<?php

namespace EnderLab\BlameableBundle\Listener;

use App\System\Infrastructure\Symfony\Security\SecurityUser;
use EnderLab\BlameableBundle\Interface\BlameableInterface;
use Doctrine\Bundle\DoctrineBundle\Attribute\AsDoctrineListener;
use Doctrine\ORM\Event\PrePersistEventArgs;
use Doctrine\ORM\Event\PreUpdateEventArgs;
use Doctrine\ORM\Events;
use Symfony\Bundle\SecurityBundle\Security;
use Symfony\Component\Security\Core\User\UserInterface;

#[AsDoctrineListener(event: Events::prePersist)]
#[AsDoctrineListener(event: Events::preUpdate)]
readonly class BlameableListener
{
    public function __construct(
        private Security $security,
    ) {

    }

    public function prePersist(PrePersistEventArgs $args): void
    {
        $entity = $args->getObject();
        $securityUser = $this->getUser();

        if ($entity instanceof BlameableInterface && $securityUser instanceof UserInterface) {
            $entity->setCreatedBy('/api/customer/users/'.$securityUser->getId());
        }
    }

    public function preUpdate(PreUpdateEventArgs $args): void
    {
        $entity = $args->getObject();
        $securityUser = $this->getUser();

        if ($entity instanceof BlameableInterface && $securityUser instanceof UserInterface) {
            $entity->setUpdatedBy('/api/customer/users/'.$securityUser->getId());
        }
    }

    private function getUser(): ?UserInterface
    {
        return $this->security->getUser();
    }
}

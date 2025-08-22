<?php

namespace App\System\Infrastructure\Symfony\Command;

use App\System\Domain\Model\User;
use App\System\Domain\Model\UserStatus;
use App\System\Domain\Model\UserType;
use App\System\Infrastructure\Symfony\Security\SecurityUser;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\Console\Attribute\AsCommand;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;

#[AsCommand(
    name: 'marvin:system:test',
    description: 'Test command',
)]
class TestCommand extends Command
{
    public function __construct(
        private readonly EntityManagerInterface $em,
        private readonly UserPasswordHasherInterface $hasher,
    ) {
        parent::__construct();
    }

    protected function configure(): void
    {

    }

    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        $userType = $this->em->getRepository(UserType::class)->findOneBy(['reference' => UserType::TYPE_APPLICATION]);
        $userStatus = $this->em->getRepository(UserStatus::class)->findOneBy(['reference' => UserStatus::STATUS_ENABLED]);
        $user = new User();
        $securityUser = new SecurityUser();
        $user
            ->setEmail('test@test'.rand(1, 10000).'.com')
            ->setRoles(['ROLE_USER'])
            ->setFirstName('Test')
            ->setLastName('Test')
            ->setType($userType)
            ->setStatus($userStatus)
            ->setPassword($this->hasher->hashPassword($securityUser, 'Test1234'))
        ;

        $this->em->persist($user);
        $this->em->flush();

        return Command::SUCCESS;
    }
}

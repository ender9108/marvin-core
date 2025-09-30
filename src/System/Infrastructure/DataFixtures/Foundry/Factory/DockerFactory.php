<?php

namespace Marvin\Security\Infrastructure\Framework\Symfony\DataFixtures\Foundry\Factory;

use Marvin\Security\Domain\List\Role;
use Marvin\Security\Domain\List\UserStatusReference;
use Marvin\Security\Domain\List\UserTypeReference;
use Marvin\Security\Domain\Model\User;
use Marvin\Security\Domain\Service\PasswordHasherInterface;
use Marvin\Security\Domain\ValueObject\Firstname;
use Marvin\Security\Domain\ValueObject\Lastname;
use Marvin\Security\Domain\ValueObject\Roles;
use Marvin\Shared\Domain\ValueObject\Email;
use Marvin\System\Domain\Model\Docker;
use Symfony\Component\DependencyInjection\ParameterBag\ParameterBagInterface;
use Symfony\Component\Yaml\Yaml;
use Zenstruck\Foundry\Persistence\PersistentProxyObjectFactory;

class DockerFactory extends PersistentProxyObjectFactory
{
    private static array $datas = [];

    public function __construct(
        private readonly ParameterBagInterface $parameters,
    ) {
        parent::__construct();
        $this->initializeDatas();
    }

    private function initializeDatas(): void
    {
        $rootPath = $this->parameters->get('kernel.project_dir');
        $composeFile = $rootPath . '/docker-compose.yml';
        $dockerCompose = new Yaml();
        $dockerComposeDatas = $dockerCompose->parseFile($composeFile);

        dd($dockerComposeDatas);
    }

    protected function defaults(): array|callable
    {
        return [];
    }

    #[\Override]
    protected function initialize(): static
    {
        return $this
            ->beforeInstantiate(function (array $parameters): array {
                $parameters['firstname'] = new Firstname($parameters['firstname']);
                $parameters['lastname'] = new Lastname($parameters['lastname']);
                $parameters['email'] = new Email($parameters['email']);
                $parameters['roles'] = new Roles($parameters['roles']);

                return $parameters;
            })
            ->afterInstantiate(function (User $user): void {
                $user->definePassword($user->password, $this->passwordHasher);
            })
        ;
    }

    public static function getDatas(): array
    {
        return self::$datas;
    }

    public static function class(): string
    {
        return Docker::class;
    }
}

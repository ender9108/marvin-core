<?php

namespace App\System\Infrastructure\ApiPlatform\Mapper\User;

use App\System\Domain\Model\User;
use App\System\Domain\Model\UserStatus;
use App\System\Domain\Model\UserType;
use App\System\Infrastructure\ApiPlatform\Resource\UserResource;
use App\System\Infrastructure\Symfony\Security\SecurityUser;
use EnderLab\DddCqrsBundle\Application\Query\FindItemQuery;
use EnderLab\DddCqrsBundle\Application\Query\Bus\QueryBus;
use EnderLab\DddCqrsBundle\Domain\Exception\MissingModelException;
use Symfony\Component\Messenger\Exception\ExceptionInterface;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;
use Symfonycasts\MicroMapper\AsMapper;
use Symfonycasts\MicroMapper\MapperInterface;
use Symfonycasts\MicroMapper\MicroMapperInterface;
use EnderLab\DddCqrsApiPlatformBundle\Mapper\AbstractMapper;
use Symfony\Contracts\Translation\TranslatorInterface;
use Symfony\Contracts\Cache\CacheInterface;

#[AsMapper(from: UserResource::class, to: User::class)]
class UserResourceToEntityMapper extends AbstractMapper implements MapperInterface
{
    public function __construct(
        private readonly QueryBus $queryBus,
        private readonly MicroMapperInterface $microMapper,
        private readonly UserPasswordHasherInterface $passwordHasher,
        TranslatorInterface $translator,
        CacheInterface $cache,
    ) {
        parent::__construct($translator, $cache);
    }

    /**
     * @throws ExceptionInterface
     * @throws MissingModelException
     */
    public function load(object $from, string $toClass, array $context): object
    {
        $dto = $from;
        assert($dto instanceof UserResource);

        $entity = $dto->id ?
            $this->queryBus->ask(new FindItemQuery($dto->id, User::class)) :
            new User()
        ;

        if (!$entity) {
            throw new MissingModelException($dto->id, User::class);
        }

        return $entity;
    }

    public function populate(object $from, object $to, array $context): object
    {
        $dto = $from;
        $entity = $to;

        assert($dto instanceof UserResource);
        assert($entity instanceof User);

        $entity->setFirstName($dto->firstName);
        $entity->setLastName($dto->lastName);
        $entity->setEmail($dto->email);
        $entity->setRoles($dto->roles);
        $entity->setStatus($this->microMapper->map($dto->status, UserStatus::class, [
            MicroMapperInterface::MAX_DEPTH => 0
        ]));
        $entity->setType($this->microMapper->map($dto->type, UserType::class, [
            MicroMapperInterface::MAX_DEPTH => 0
        ]));

        if ($dto->password) {
            $entity->setPassword($this->passwordHasher->hashPassword(new SecurityUser(), $dto->password));
        }

        return $entity;
    }
}

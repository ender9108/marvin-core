<?php
/**
 * Marvin Core - DDD-based home automation system
 *
 * @package   Marvin\Core
 * @author    Alexandre Berthelot <alexandreberthelot9108@gmail.com>
 * @copyright 2024-present Alexandre Berthelot
 * @license   AGPL-3.0 License
 * @link      https://github.com/ender9108/marvin-core
 */

declare(strict_types=1);

namespace Marvin\Security\Infrastructure\Framework\Symfony\Mapper;

use EnderLab\DddCqrsBundle\Domain\Assert\Assert;
use Marvin\Security\Domain\Model\User;
use Marvin\Security\Presentation\Api\Resource\ReadUserResource;
use Symfonycasts\MicroMapper\AsMapper;
use Symfonycasts\MicroMapper\MapperInterface;

#[AsMapper(from: User::class, to: ReadUserResource::class)]
class UserEntityToResource implements MapperInterface
{
    public function load(object $from, string $toClass, array $context): object
    {
        /** @var User $entity */
        $entity = $from;

        Assert::isInstanceOf($entity, User::class);

        $resource = new ReadUserResource();

        $resource->id = $entity->id->toString();

        return $resource;
    }

    public function populate(object $from, object $to, array $context): object
    {
        /** @var User $entity */
        $entity = $from;
        /** @var ReadUserResource $resource */
        $resource = $to;

        Assert::isInstanceOf($entity, User::class);
        Assert::isInstanceOf($resource, ReadUserResource::class);

        $resource->email = $entity->email->value;
        $resource->firstname = $entity->firstname->value;
        $resource->lastname = $entity->lastname->value;
        $resource->type = $entity->type->value;
        $resource->status = $entity->status->value;
        $resource->locale = $entity->locale->value;
        $resource->timezone = $entity->timezone->value;
        $resource->theme = $entity->theme->value;
        $resource->roles = $entity->roles->toArray();
        $resource->createdAt = $entity->createdAt;
        $resource->updatedAt = $entity->updatedAt;

        return $resource;
    }
}

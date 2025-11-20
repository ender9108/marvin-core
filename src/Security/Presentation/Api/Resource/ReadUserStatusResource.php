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

namespace Marvin\Security\Presentation\Api\Resource;

use ApiPlatform\Doctrine\Orm\State\Options;
use ApiPlatform\Metadata\ApiProperty;
use ApiPlatform\Metadata\ApiResource;
use ApiPlatform\Metadata\Delete;
use ApiPlatform\Metadata\Get;
use ApiPlatform\Metadata\GetCollection;
use ApiPlatform\Metadata\Patch;
use ApiPlatform\Metadata\Post;
use DateTimeInterface;
use EnderLab\DddCqrsApiPlatformBundle\Infrastructure\Framework\ApiPlatform\State\Provider\EntityToApiStateProvider;
use Marvin\Security\Domain\Model\User;
use Marvin\Security\Presentation\Api\Dto\Input\ChangeEmailUserDto;
use Marvin\Security\Presentation\Api\Dto\Input\ChangePasswordUserDto;
use Marvin\Security\Presentation\Api\Dto\Input\CreateUserDto;
use Marvin\Security\Presentation\Api\Dto\Input\RequestResetPasswordUserDto;
use Marvin\Security\Presentation\Api\Dto\Input\ResetPasswordUserDto;
use Marvin\Security\Presentation\Api\Dto\Input\UpdateProfileUserDto;
use Marvin\Security\Presentation\Api\State\Processor\ChangeEmailUserProcessor;
use Marvin\Security\Presentation\Api\State\Processor\ChangePasswordUserProcessor;
use Marvin\Security\Presentation\Api\State\Processor\CreateUserProcessor;
use Marvin\Security\Presentation\Api\State\Processor\DeleteUserProcessor;
use Marvin\Security\Presentation\Api\State\Processor\DisableUserProcessor;
use Marvin\Security\Presentation\Api\State\Processor\EnableUserProcessor;
use Marvin\Security\Presentation\Api\State\Processor\LockUserProcessor;
use Marvin\Security\Presentation\Api\State\Processor\RequestResetPasswordUserProcessor;
use Marvin\Security\Presentation\Api\State\Processor\ResetPasswordUserProcessor;
use Marvin\Security\Presentation\Api\State\Processor\UpdateProfileUserProcessor;

#[ApiResource(
    shortName: 'user_status',
    operations: [
        new Get(
            provider: ''
        ),
        new GetCollection(
            provider: ''
        ),
    ],
    routePrefix: '/security',
    provider: EntityToApiStateProvider::class,
    stateOptions: new Options(entityClass: User::class)
)]
final class ReadUserStatusResource
{
    #[ApiProperty(writable: false, identifier: true)]
    public string $reference;

    public string $label;
}

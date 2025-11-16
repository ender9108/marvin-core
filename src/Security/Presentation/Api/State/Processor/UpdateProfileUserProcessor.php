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

namespace Marvin\Security\Presentation\Api\State\Processor;

use ApiPlatform\Metadata\Operation;
use ApiPlatform\State\ProcessorInterface;
use EnderLab\DddCqrsBundle\Application\Command\SyncCommandBusInterface;
use EnderLab\DddCqrsBundle\Domain\Assert\Assert;
use Exception;
use Marvin\Security\Application\Command\User\UpdateProfileUser;
use Marvin\Security\Domain\ValueObject\Firstname;
use Marvin\Security\Domain\ValueObject\Lastname;
use Marvin\Security\Domain\ValueObject\Roles;
use Marvin\Security\Domain\ValueObject\Timezone;
use Marvin\Security\Presentation\Api\Dto\Input\UpdateProfileUserDto;
use Marvin\Security\Presentation\Api\Resource\ReadUserResource;
use Marvin\Shared\Domain\ValueObject\Identity\UserId;
use Marvin\Shared\Domain\ValueObject\Locale;
use Marvin\Shared\Domain\ValueObject\Theme;
use Symfonycasts\MicroMapper\MicroMapperInterface;

final readonly class UpdateProfileUserProcessor implements ProcessorInterface
{
    public function __construct(
        private MicroMapperInterface $microMapper,
        private SyncCommandBusInterface $syncCommandBus,
    ) {
    }

    /**
     * @param UpdateProfileUserDto $data
     * @throws Exception
     */
    public function process(mixed $data, Operation $operation, array $uriVariables = [], array $context = []): ReadUserResource
    {
        Assert::isInstanceOf($data, UpdateProfileUserDto::class);

        $user = $this->syncCommandBus->handle(new UpdateProfileUser(
            new UserId($uriVariables['id']),
            null !== $data->firstname ? Firstname::fromString($data->firstname) : null,
            null !== $data->lastname ? Lastname::fromString($data->lastname) : null,
            !empty($data->roles) ? Roles::fromArray($data->roles) : null,
            null !== $data->theme ? Theme::fromString($data->theme) : null,
            null !== $data->locale ? Locale::fromString($data->locale) : null,
            null !== $data->timezone ? Timezone::fromString($data->timezone) : null,
        ));

        return $this->microMapper->map($user, ReadUserResource::class);
    }
}

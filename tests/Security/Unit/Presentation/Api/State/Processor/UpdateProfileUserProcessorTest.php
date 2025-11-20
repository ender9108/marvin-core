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

namespace Marvin\Tests\Security\Unit\Presentation\Api\State\Processor;

use ApiPlatform\Metadata\Patch;
use EnderLab\DddCqrsBundle\Application\Command\SyncCommandBusInterface;
use Marvin\Security\Application\Command\User\UpdateProfileUser;
use Marvin\Security\Domain\Model\User;
use Marvin\Security\Domain\ValueObject\Firstname;
use Marvin\Security\Domain\ValueObject\Lastname;
use Marvin\Security\Domain\ValueObject\Roles;
use Marvin\Security\Domain\ValueObject\Timezone;
use Marvin\Security\Domain\ValueObject\UserStatus;
use Marvin\Security\Domain\ValueObject\UserType;
use Marvin\Security\Presentation\Api\Dto\Input\UpdateProfileUserDto;
use Marvin\Security\Presentation\Api\Resource\ReadUserResource;
use Marvin\Security\Presentation\Api\State\Processor\UpdateProfileUserProcessor;
use Marvin\Shared\Domain\ValueObject\Email;
use Marvin\Shared\Domain\ValueObject\Identity\UserId;
use Marvin\Shared\Domain\ValueObject\Locale;
use Marvin\Shared\Domain\ValueObject\Theme;
use PHPUnit\Framework\MockObject\MockObject;
use PHPUnit\Framework\TestCase;
use Symfonycasts\MicroMapper\MicroMapperInterface;

final class UpdateProfileUserProcessorTest extends TestCase
{
    private MicroMapperInterface|MockObject $microMapper;
    private SyncCommandBusInterface|MockObject $syncCommandBus;
    private UpdateProfileUserProcessor $processor;

    protected function setUp(): void
    {
        $this->microMapper = $this->createMock(MicroMapperInterface::class);
        $this->syncCommandBus = $this->createMock(SyncCommandBusInterface::class);

        $this->processor = new UpdateProfileUserProcessor(
            $this->microMapper,
            $this->syncCommandBus
        );
    }

    public function testSuccessfulProfileUpdate(): void
    {
        $userId = new UserId();
        $dto = new UpdateProfileUserDto();
        $dto->firstname = 'Jane';
        $dto->lastname = 'Smith';
        $dto->roles = ['ROLE_ADMIN'];
        $dto->locale = 'fr';
        $dto->theme = 'dark';
        $dto->timezone = 'Europe/Paris';

        $user = User::create(
            Email::fromString('test@example.com'),
            Firstname::fromString('Jane'),
            Lastname::fromString('Smith'),
            UserStatus::enabled(),
            UserType::APP,
            Timezone::fromString('Europe/Paris'),
            Roles::admin(),
            Locale::fromString('fr'),
            Theme::fromString('dark')
        );

        $resource = new ReadUserResource();
        $resource->id = $userId->toString();
        $resource->firstname = 'Jane';
        $resource->lastname = 'Smith';

        $this->syncCommandBus
            ->expects($this->once())
            ->method('handle')
            ->with($this->isInstanceOf(UpdateProfileUser::class))
            ->willReturn($user);

        $this->microMapper
            ->expects($this->once())
            ->method('map')
            ->with($user, ReadUserResource::class)
            ->willReturn($resource);

        $result = $this->processor->process($dto, new Patch(), ['id' => $userId->toString()]);

        $this->assertInstanceOf(ReadUserResource::class, $result);
        $this->assertEquals('Jane', $result->firstname);
        $this->assertEquals('Smith', $result->lastname);
    }
}

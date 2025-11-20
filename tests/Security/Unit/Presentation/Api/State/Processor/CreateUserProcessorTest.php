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

use ApiPlatform\Metadata\Post;
use EnderLab\DddCqrsBundle\Application\Command\SyncCommandBusInterface;
use InvalidArgumentException;
use Marvin\Security\Application\Command\User\CreateUser;
use Marvin\Security\Domain\Model\User;
use Marvin\Security\Domain\ValueObject\Firstname;
use Marvin\Security\Domain\ValueObject\Lastname;
use Marvin\Security\Domain\ValueObject\Roles;
use Marvin\Security\Domain\ValueObject\Timezone;
use Marvin\Security\Domain\ValueObject\UserStatus;
use Marvin\Security\Domain\ValueObject\UserType;
use Marvin\Security\Presentation\Api\Dto\Input\CreateUserDto;
use Marvin\Security\Presentation\Api\Resource\ReadUserResource;
use Marvin\Security\Presentation\Api\State\Processor\CreateUserProcessor;
use Marvin\Shared\Domain\ValueObject\Email;
use Marvin\Shared\Domain\ValueObject\Locale;
use Marvin\Shared\Domain\ValueObject\Theme;
use PHPUnit\Framework\MockObject\MockObject;
use PHPUnit\Framework\TestCase;
use Symfonycasts\MicroMapper\MicroMapperInterface;

final class CreateUserProcessorTest extends TestCase
{
    private MicroMapperInterface|MockObject $microMapper;
    private SyncCommandBusInterface|MockObject $syncCommandBus;
    private CreateUserProcessor $processor;

    protected function setUp(): void
    {
        $this->microMapper = $this->createMock(MicroMapperInterface::class);
        $this->syncCommandBus = $this->createMock(SyncCommandBusInterface::class);

        $this->processor = new CreateUserProcessor(
            $this->microMapper,
            $this->syncCommandBus
        );
    }

    public function testSuccessfulUserCreation(): void
    {
        $dto = new CreateUserDto();
        $dto->email = 'test@example.com';
        $dto->firstname = 'John';
        $dto->lastname = 'Doe';
        $dto->roles = ['ROLE_USER'];
        $dto->locale = 'en';
        $dto->theme = 'light';
        $dto->timezone = 'UTC';
        $dto->password = 'SecurePassword123!';

        $user = User::create(
            Email::fromString('test@example.com'),
            Firstname::fromString('John'),
            Lastname::fromString('Doe'),
            UserStatus::enabled(),
            UserType::APP,
            Timezone::fromString('UTC'),
            Roles::user(),
            Locale::fromString('en'),
            Theme::fromString('light')
        );

        $resource = new ReadUserResource();
        $resource->id = $user->id->toString();
        $resource->email = 'test@example.com';

        $this->syncCommandBus
            ->expects($this->once())
            ->method('handle')
            ->with($this->isInstanceOf(CreateUser::class))
            ->willReturn($user);

        $this->microMapper
            ->expects($this->once())
            ->method('map')
            ->with($user, ReadUserResource::class)
            ->willReturn($resource);

        $result = $this->processor->process($dto, new Post());

        $this->assertInstanceOf(ReadUserResource::class, $result);
        $this->assertEquals('test@example.com', $result->email);
    }

    public function testThrowsExceptionWhenInvalidDataType(): void
    {
        $invalidData = new \stdClass();

        $this->expectException(\TypeError::class);

        $this->processor->process($invalidData, new Post());
    }
}

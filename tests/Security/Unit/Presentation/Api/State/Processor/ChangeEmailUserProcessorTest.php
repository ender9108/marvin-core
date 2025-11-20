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
use Marvin\Security\Application\Command\User\ChangeEmailUser;
use Marvin\Security\Domain\Model\User;
use Marvin\Security\Domain\ValueObject\Firstname;
use Marvin\Security\Domain\ValueObject\Lastname;
use Marvin\Security\Domain\ValueObject\Roles;
use Marvin\Security\Domain\ValueObject\Timezone;
use Marvin\Security\Domain\ValueObject\UserStatus;
use Marvin\Security\Domain\ValueObject\UserType;
use Marvin\Security\Presentation\Api\Dto\Input\ChangeEmailUserDto;
use Marvin\Security\Presentation\Api\Resource\ReadUserResource;
use Marvin\Security\Presentation\Api\State\Processor\ChangeEmailUserProcessor;
use Marvin\Shared\Domain\ValueObject\Email;
use Marvin\Shared\Domain\ValueObject\Identity\UserId;
use Marvin\Shared\Domain\ValueObject\Locale;
use Marvin\Shared\Domain\ValueObject\Theme;
use PHPUnit\Framework\MockObject\MockObject;
use PHPUnit\Framework\TestCase;
use Symfonycasts\MicroMapper\MicroMapperInterface;

final class ChangeEmailUserProcessorTest extends TestCase
{
    private MicroMapperInterface|MockObject $microMapper;
    private SyncCommandBusInterface|MockObject $syncCommandBus;
    private ChangeEmailUserProcessor $processor;

    protected function setUp(): void
    {
        $this->microMapper = $this->createMock(MicroMapperInterface::class);
        $this->syncCommandBus = $this->createMock(SyncCommandBusInterface::class);

        $this->processor = new ChangeEmailUserProcessor(
            $this->microMapper,
            $this->syncCommandBus
        );
    }

    /**
     * @throws \Exception
     */
    public function testSuccessfulEmailChange(): void
    {
        $userId = new UserId();
        $dto = new ChangeEmailUserDto();
        $dto->email = 'newemail@example.com';

        $user = User::create(
            Email::fromString('newemail@example.com'),
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
        $resource->id = $userId->toString();
        $resource->email = 'newemail@example.com';

        $this->syncCommandBus
            ->expects($this->once())
            ->method('handle')
            ->with($this->isInstanceOf(ChangeEmailUser::class))
            ->willReturn($user);

        $this->microMapper
            ->expects($this->once())
            ->method('map')
            ->with($user, ReadUserResource::class)
            ->willReturn($resource);

        $result = $this->processor->process($dto, new Patch(), ['id' => $userId->toString()]);

        $this->assertInstanceOf(ReadUserResource::class, $result);
        $this->assertEquals('newemail@example.com', $result->email);
    }
}

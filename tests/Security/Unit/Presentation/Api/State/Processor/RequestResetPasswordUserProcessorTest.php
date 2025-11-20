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
use EnderLab\DddCqrsBundle\Application\Command\CommandBusInterface;
use Marvin\Security\Application\Command\User\RequestResetPasswordUser;
use Marvin\Security\Domain\Model\User;
use Marvin\Security\Domain\ValueObject\Firstname;
use Marvin\Security\Domain\ValueObject\Lastname;
use Marvin\Security\Domain\ValueObject\Roles;
use Marvin\Security\Domain\ValueObject\Timezone;
use Marvin\Security\Domain\ValueObject\UserStatus;
use Marvin\Security\Domain\ValueObject\UserType;
use Marvin\Security\Presentation\Api\Dto\Input\RequestResetPasswordUserDto;
use Marvin\Security\Presentation\Api\Resource\ReadUserResource;
use Marvin\Security\Presentation\Api\State\Processor\RequestResetPasswordUserProcessor;
use Marvin\Shared\Domain\ValueObject\Email;
use Marvin\Shared\Domain\ValueObject\Locale;
use Marvin\Shared\Domain\ValueObject\Theme;
use PHPUnit\Framework\MockObject\MockObject;
use PHPUnit\Framework\TestCase;

final class RequestResetPasswordUserProcessorTest extends TestCase
{
    private CommandBusInterface|MockObject $syncCommandBus;
    private RequestResetPasswordUserProcessor $processor;

    protected function setUp(): void
    {
        $this->syncCommandBus = $this->createMock(CommandBusInterface::class);

        $this->processor = new RequestResetPasswordUserProcessor(
            $this->syncCommandBus
        );
    }

    public function testSuccessfulPasswordResetRequest(): void
    {
        $dto = new RequestResetPasswordUserDto();
        $dto->email = 'test@example.com';

        $resource = new ReadUserResource();
        $resource->email = 'test@example.com';

        $this->syncCommandBus
            ->expects($this->once())
            ->method('dispatch')
            ->with($this->isInstanceOf(RequestResetPasswordUser::class));

        $result = $this->processor->process($dto, new Post(), [], ['previous_data' => $resource]);

        $this->assertInstanceOf(ReadUserResource::class, $result);
        $this->assertEquals('test@example.com', $result->email);
    }
}

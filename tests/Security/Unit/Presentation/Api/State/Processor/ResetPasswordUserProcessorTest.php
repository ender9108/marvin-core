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
use Marvin\Security\Application\Command\User\ResetPasswordUser;
use Marvin\Security\Presentation\Api\Dto\Input\ResetPasswordUserDto;
use Marvin\Security\Presentation\Api\Resource\ReadUserResource;
use Marvin\Security\Presentation\Api\State\Processor\ResetPasswordUserProcessor;
use PHPUnit\Framework\MockObject\MockObject;
use PHPUnit\Framework\TestCase;
use Symfonycasts\MicroMapper\MicroMapperInterface;

final class ResetPasswordUserProcessorTest extends TestCase
{
    private CommandBusInterface|MockObject $syncCommandBus;
    private ResetPasswordUserProcessor $processor;

    protected function setUp(): void
    {
        $this->syncCommandBus = $this->createMock(CommandBusInterface::class);

        $this->processor = new ResetPasswordUserProcessor(
            $this->syncCommandBus
        );
    }

    public function testSuccessfulPasswordReset(): void
    {
        $dto = new ResetPasswordUserDto();
        $dto->token = 'valid_token_123';
        $dto->password = 'NewPassword456!';

        $resource = new ReadUserResource();
        $resource->email = 'test@example.com';

        $this->syncCommandBus
            ->expects($this->once())
            ->method('dispatch')
            ->with($this->callback(function (ResetPasswordUser $command) use ($dto) {
                return $command->token === $dto->token && $command->password === $dto->password;
            }));

        $result = $this->processor->process($dto, new Post(), [], ['previous_data' => $resource]);

        $this->assertInstanceOf(ReadUserResource::class, $result);
        $this->assertEquals('test@example.com', $result->email);
    }
}

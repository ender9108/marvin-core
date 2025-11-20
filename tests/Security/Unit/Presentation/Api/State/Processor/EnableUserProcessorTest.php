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
use Marvin\Security\Application\Command\User\EnableUser;
use Marvin\Security\Presentation\Api\State\Processor\EnableUserProcessor;
use Marvin\Shared\Domain\ValueObject\Identity\UserId;
use PHPUnit\Framework\MockObject\MockObject;
use PHPUnit\Framework\TestCase;
use Symfonycasts\MicroMapper\MicroMapperInterface;

final class EnableUserProcessorTest extends TestCase
{
    private MicroMapperInterface|MockObject $microMapper;
    private SyncCommandBusInterface|MockObject $syncCommandBus;
    private EnableUserProcessor $processor;

    protected function setUp(): void
    {
        $this->microMapper = $this->createMock(MicroMapperInterface::class);
        $this->syncCommandBus = $this->createMock(SyncCommandBusInterface::class);

        $this->processor = new EnableUserProcessor($this->microMapper, $this->syncCommandBus);
    }

    public function testSuccessfulUserEnabling(): void
    {
        $userId = new UserId();
        $resource = new \Marvin\Security\Presentation\Api\Resource\ReadUserResource();

        $this->syncCommandBus
            ->expects($this->once())
            ->method('handle')
            ->with($this->callback(function (EnableUser $command) use ($userId) {
                return $command->id->toString() === $userId->toString();
            }))
            ->willReturn(new \stdClass());

        $this->microMapper
            ->expects($this->once())
            ->method('map')
            ->willReturn($resource);

        $this->processor->process($resource, new Patch(), ['id' => $userId->toString()]);
    }
}

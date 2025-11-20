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

use ApiPlatform\Metadata\Delete;
use EnderLab\DddCqrsBundle\Application\Command\SyncCommandBusInterface;
use Marvin\Security\Application\Command\User\DeleteUser;
use Marvin\Security\Presentation\Api\State\Processor\DeleteUserProcessor;
use Marvin\Shared\Domain\ValueObject\Identity\UserId;
use PHPUnit\Framework\MockObject\MockObject;
use PHPUnit\Framework\TestCase;

final class DeleteUserProcessorTest extends TestCase
{
    private SyncCommandBusInterface|MockObject $syncCommandBus;
    private DeleteUserProcessor $processor;

    protected function setUp(): void
    {
        $this->syncCommandBus = $this->createMock(SyncCommandBusInterface::class);

        $this->processor = new DeleteUserProcessor($this->syncCommandBus);
    }

    public function testSuccessfulUserDeletion(): void
    {
        $userId = new UserId();

        $this->syncCommandBus
            ->expects($this->once())
            ->method('handle')
            ->with($this->callback(function (DeleteUser $command) use ($userId) {
                return $command->id->toString() === $userId->toString();
            }));

        $this->processor->process(null, new Delete(), ['id' => $userId->toString()]);
    }
}

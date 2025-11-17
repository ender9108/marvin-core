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

namespace Marvin\Tests\Security\Unit\Application\QueryHandler;

use EnderLab\DddCqrsBundle\Domain\Repository\PaginatorInterface;
use Marvin\Security\Application\Query\GetUsersCollection;
use Marvin\Security\Application\QueryHandler\GetUsersCollectionHandler;
use Marvin\Security\Domain\Repository\UserRepositoryInterface;
use PHPUnit\Framework\MockObject\MockObject;
use PHPUnit\Framework\TestCase;

final class GetUsersCollectionHandlerTest extends TestCase
{
    private UserRepositoryInterface|MockObject $userRepository;
    private GetUsersCollectionHandler $handler;

    protected function setUp(): void
    {
        $this->userRepository = $this->createMock(UserRepositoryInterface::class);

        $this->handler = new GetUsersCollectionHandler($this->userRepository);
    }

    public function testSuccessfulCollectionRetrieval(): void
    {
        $criterias = ['status' => 'enabled'];
        $orderBy = ['email' => 'ASC'];
        $page = 1;
        $itemsPerPage = 20;

        $query = new GetUsersCollection($criterias, $orderBy, $page, $itemsPerPage);

        $mockPaginator = $this->createMock(PaginatorInterface::class);

        $this->userRepository
            ->expects($this->once())
            ->method('collection')
            ->with($criterias, $orderBy, $page, $itemsPerPage)
            ->willReturn($mockPaginator);

        $result = ($this->handler)($query);

        $this->assertInstanceOf(PaginatorInterface::class, $result);
    }

    public function testCollectionRetrievalWithDefaultParameters(): void
    {
        $query = new GetUsersCollection();

        $mockPaginator = $this->createMock(PaginatorInterface::class);

        $this->userRepository
            ->expects($this->once())
            ->method('collection')
            ->with([], [], 1, 20)
            ->willReturn($mockPaginator);

        $result = ($this->handler)($query);

        $this->assertInstanceOf(PaginatorInterface::class, $result);
    }

    public function testCollectionRetrievalWithCustomPagination(): void
    {
        $query = new GetUsersCollection([], [], 2, 50);

        $mockPaginator = $this->createMock(PaginatorInterface::class);

        $this->userRepository
            ->expects($this->once())
            ->method('collection')
            ->with([], [], 2, 50)
            ->willReturn($mockPaginator);

        $result = ($this->handler)($query);

        $this->assertInstanceOf(PaginatorInterface::class, $result);
    }
}

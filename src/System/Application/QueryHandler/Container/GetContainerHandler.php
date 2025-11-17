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

namespace Marvin\System\Application\QueryHandler\Container;

use EnderLab\DddCqrsBundle\Application\Query\QueryHandlerInterface;
use Marvin\System\Application\Query\Container\GetContainer;
use Marvin\System\Domain\Model\Container;
use Marvin\System\Domain\Repository\ContainerRepositoryInterface;
use Symfony\Component\Messenger\Attribute\AsMessageHandler;

#[AsMessageHandler]
final readonly class GetContainerHandler implements QueryHandlerInterface
{
    public function __construct(
        private ContainerRepositoryInterface $containerRepository,
    ) {
    }

    public function __invoke(GetContainer $query): Container
    {
        return $this->containerRepository->byId($query->id);
    }
}

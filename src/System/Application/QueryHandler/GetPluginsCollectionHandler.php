<?php


namespace Marvin\System\Application\QueryHandler;

use EnderLab\DddCqrsBundle\Application\Query\QueryHandlerInterface;
use EnderLab\DddCqrsBundle\Infrastructure\Persistence\Doctrine\ORM\PaginatorOrm;
use Marvin\System\Application\Query\GetPluginsCollection;
use Marvin\System\Domain\Repository\PluginRepositoryInterface;
use Symfony\Component\Messenger\Attribute\AsMessageHandler;

#[AsMessageHandler]
final readonly class GetPluginsCollectionHandler implements QueryHandlerInterface
{
    public function __construct(
        private readonly PluginRepositoryInterface $pluginRepository,
    ) {
    }

    public function __invoke(GetPluginsCollection $query): PaginatorOrm
    {
        return $this->pluginRepository->collection(
            $query->criteria,
            $query->orderBy,
            $query->page,
            $query->itemsPerPage,
        );
    }
}

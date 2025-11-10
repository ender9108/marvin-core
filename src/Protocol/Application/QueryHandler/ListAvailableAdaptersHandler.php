<?php

declare(strict_types=1);

namespace Marvin\Protocol\Application\QueryHandler;

use EnderLab\DddCqrsBundle\Application\Query\QueryHandlerInterface;
use Marvin\Protocol\Application\Query\ListAvailableAdapters;
use Marvin\Protocol\Application\Service\Acl\Dto\AdapterDto;
use Marvin\Protocol\Domain\Model\ProtocolAdapterInterface;
use Symfony\Component\DependencyInjection\Attribute\AutowireIterator;
use Symfony\Component\Messenger\Attribute\AsMessageHandler;

#[AsMessageHandler]
final readonly class ListAvailableAdaptersHandler implements QueryHandlerInterface
{
    /**
     * @param iterable<ProtocolAdapterInterface> $adapters
     */
    public function __construct(
        #[AutowireIterator(tag: 'protocol.adapter')]
        private iterable $adapters,
    ) {
    }

    /**
     * @return array<AdapterDto>
     */
    public function __invoke(ListAvailableAdapters $query): array
    {
        $result = [];

        foreach ($this->adapters as $adapter) {
            if ($query->protocolType !== null && $adapter->getProtocolType() !== $query->protocolType) {
                continue;
            }

            $result[] = new AdapterDto(
                name: $adapter->getName(),
                className: $adapter::class,
                protocolType: $adapter->getProtocolType(),
                defaultExecutionMode: $adapter->getDefaultExecutionMode()->value,
                supportedExecutionModes: $adapter->getSupportedExecutionModes(),
                description: $adapter->getDescription(),
            );
        }

        return $result;
    }
}

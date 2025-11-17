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

namespace Marvin\System\Application\QueryHandler\ActionRequest;

use EnderLab\DddCqrsBundle\Application\Query\QueryHandlerInterface;
use Marvin\System\Application\Query\ActionRequest\GetActionRequestByCorrelationid;
use Marvin\System\Domain\Model\ActionRequest;
use Marvin\System\Domain\Repository\ActionRequestRepositoryInterface;
use Symfony\Component\Messenger\Attribute\AsMessageHandler;

#[AsMessageHandler]
final readonly class GetActionRequestByCorrelationIdHandler implements QueryHandlerInterface
{
    public function __construct(
        private ActionRequestRepositoryInterface $actionRequestRepository,
    ) {
    }

    public function __invoke(GetActionRequestByCorrelationid $query): ActionRequest
    {
        return $this->actionRequestRepository->byCorrelationId($query->correlationId);
    }
}

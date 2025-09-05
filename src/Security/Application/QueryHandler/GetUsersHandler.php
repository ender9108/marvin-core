<?php
namespace Marvin\Security\Application\QueryHandler;

use EnderLab\DddCqrsBundle\Application\Query\QueryHandlerInterface;
use Marvin\Security\Application\Query\GetUsers;
use Symfony\Component\Messenger\Attribute\AsMessageHandler;

#[AsMessageHandler]
final readonly class GetUsersHandler implements QueryHandlerInterface
{
    public function __construct(
    ) {
    }

    public function __invoke(GetUsers $query): array
    {

    }
}

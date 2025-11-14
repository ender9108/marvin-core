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

namespace Marvin\Secret\Application\QueryHandler;

use EnderLab\DddCqrsBundle\Application\Query\QueryHandlerInterface;
use Marvin\Secret\Application\Query\GetSecret;
use Marvin\Secret\Domain\Exception\SecretNotFound;
use Marvin\Secret\Domain\Model\Secret;
use Marvin\Secret\Domain\Repository\SecretRepositoryInterface;
use Symfony\Component\Messenger\Attribute\AsMessageHandler;

#[AsMessageHandler]
final readonly class GetSecretHandler implements QueryHandlerInterface
{
    public function __construct(
        private SecretRepositoryInterface $secretRepository,
    ) {
    }

    public function __invoke(GetSecret $query): Secret
    {
        $secret = $this->secretRepository->byKey($query->key);

        if ($secret === null) {
            throw SecretNotFound::withKey($query->key);
        }

        return $secret;
    }
}

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

namespace Marvin\Secret\Presentation\Api\State\Processor;

use ApiPlatform\Metadata\Operation;
use ApiPlatform\State\ProcessorInterface;
use EnderLab\DddCqrsBundle\Application\Command\SyncCommandBusInterface;
use Exception;
use Marvin\Secret\Application\Command\DeleteSecret;
use Marvin\Secret\Domain\Repository\SecretRepositoryInterface;
use Marvin\Secret\Domain\ValueObject\Identity\SecretId;

final readonly class DeleteSecretProcessor implements ProcessorInterface
{
    public function __construct(
        private SecretRepositoryInterface $secretRepository,
        private SyncCommandBusInterface $syncCommandBus,
    ) {
    }

    /**
     * @throws Exception
     */
    public function process(mixed $data, Operation $operation, array $uriVariables = [], array $context = [])
    {
        $secret = $this->secretRepository->byId(SecretId::fromString($uriVariables['id']));

        $this->syncCommandBus->handle(
            new DeleteSecret($secret->key)
        );

        return $data;
    }
}

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

namespace Marvin\Security\Presentation\Api\State\Processor;

use ApiPlatform\Metadata\Operation;
use ApiPlatform\State\ProcessorInterface;
use EnderLab\DddCqrsBundle\Application\Command\CommandBusInterface;
use EnderLab\DddCqrsBundle\Domain\Assert\Assert;
use Exception;
use Marvin\Security\Application\Command\User\RequestResetPasswordUser;
use Marvin\Security\Presentation\Api\Dto\Input\RequestResetPasswordUserDto;
use Marvin\Security\Presentation\Api\Resource\ReadUserResource;
use Marvin\Shared\Domain\ValueObject\Email;

final readonly class RequestResetPasswordUserProcessor implements ProcessorInterface
{
    public function __construct(
        private CommandBusInterface $syncCommandBus,
    ) {
    }

    /**
     * @param RequestResetPasswordUserDto $data
     * @throws Exception
     */
    public function process(mixed $data, Operation $operation, array $uriVariables = [], array $context = []): ReadUserResource
    {
        Assert::isInstanceOf($data, RequestResetPasswordUserDto::class);

        $this->syncCommandBus->dispatch(new RequestResetPasswordUser(
            new Email($data->email),
        ));

        return $context['previous_data'];
    }
}

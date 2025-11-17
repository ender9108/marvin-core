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

namespace Marvin\Security\Domain\Exception;

use EnderLab\DddCqrsBundle\Domain\Exception\DomainException;
use EnderLab\DddCqrsBundle\Domain\Exception\Interfaces\TranslatableExceptionInterface;
use Marvin\Shared\Domain\Exception\NotFoundInterface;
use Override;
use Symfony\Component\HttpFoundation\Response;

final class ModelNotFound extends DomainException implements TranslatableExceptionInterface, NotFoundInterface
{
    public function __construct(
        private readonly string $model
    ) {
        parent::__construct(sprintf('Model %s not found', $model));
    }

    public function translationId(): string
    {
        return 'security.exceptions.SC0011.model_not_found';
    }

    /** @return array<string, string> */
    public function translationParameters(): array
    {
        return [
            '%model%' => $this->model,
        ];
    }

    public function translationDomain(): string
    {
        return 'security';
    }

    #[Override]
    public function getStatusCode(): int
    {
        return Response::HTTP_NOT_FOUND;
    }
}

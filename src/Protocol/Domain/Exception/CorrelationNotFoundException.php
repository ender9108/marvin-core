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

namespace Marvin\Protocol\Domain\Exception;

use EnderLab\DddCqrsBundle\Domain\Exception\DomainException;
use EnderLab\DddCqrsBundle\Domain\Exception\Interfaces\TranslatableExceptionInterface;
use Marvin\Shared\Domain\Exception\NotFoundInterface;
use Marvin\Shared\Domain\ValueObject\Identity\CorrelationId;

class CorrelationNotFoundException extends DomainException implements TranslatableExceptionInterface, NotFoundInterface
{
    public function __construct(
        string $message,
        public readonly ?string $correlationId = null,
    ) {
        parent::__construct($message);
    }

    public static function withCorrelationId(CorrelationId $correlationId): self
    {
        return new self(
            sprintf('No pending action found for correlation ID %s', $correlationId->toString()),
            $correlationId->toString(),
        );
    }

    public function translationId(): string
    {
        if (null !== $this->correlationId) {
            return 'protocol.exceptions.PR0010.correlation_not_found_with_id';
        }

        return 'protocol.exceptions.PR0009.correlation_not_found';
    }

    public function translationParameters(): array
    {
        return [
            '%correlation_id%' => $this->correlationId,
        ];
    }

    public function translationDomain(): string
    {
        return 'protocol';
    }
}

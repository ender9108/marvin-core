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
use Marvin\Shared\Domain\ValueObject\Identity\ProtocolId;

class ProtocolConnectionException extends DomainException implements TranslatableExceptionInterface
{
    public function __construct(
        string $message,
        public readonly ?string $protocolId = null,
        public readonly ?string $errorMessage = null,
    ) {
        parent::__construct($message);
    }

    public static function withProtocol(ProtocolId $protocolId, string $errorMessage): self
    {
        return new self(
            sprintf('Connection error for protocol %s: %s', $protocolId->toString(), $errorMessage),
            $protocolId->toString(),
            $errorMessage,
        );
    }

    public function translationId(): string
    {
        if (null !== $this->protocolId) {
            return 'protocol.exceptions.PR0004.protocol_connection_error_with_id';
        }

        return 'protocol.exceptions.PR0003.protocol_connection_error';
    }

    public function translationParameters(): array
    {
        return [
            '%protocol_id%' => $this->protocolId,
            '%error_message%' => $this->errorMessage,
        ];
    }

    public function translationDomain(): string
    {
        return 'protocol';
    }
}

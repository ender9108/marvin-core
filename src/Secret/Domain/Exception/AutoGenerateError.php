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

namespace Marvin\Secret\Domain\Exception;

use EnderLab\DddCqrsBundle\Domain\Exception\DomainException;
use EnderLab\DddCqrsBundle\Domain\Exception\Interfaces\TranslatableExceptionInterface;
use Marvin\Secret\Domain\ValueObject\SecretKey;

class AutoGenerateError extends DomainException implements TranslatableExceptionInterface
{
    public function __construct(
        string $message,
        public readonly ?string $key = null,
    ) {
        parent::__construct($message);
    }

    public static function withKey(SecretKey $key): self
    {
        return new self(
            sprintf('Cannot auto-generate value for external secret %s. Please provide a new value.', $key->value),
            $key->value
        );
    }

    public function translationId(): string
    {
        if (null !== $this->key) {
            return 'secret.exceptions.SR0001.auto_generate_error_with_key';
        }

        return 'secret.exceptions.SR0002.auto_generate_error';
    }

    public function translationParameters(): array
    {
        return [
            '%key%' => $this->key
        ];
    }

    public function translationDomain(): string
    {
        return 'secret';
    }
}

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
use Marvin\Secret\Domain\ValueObject\Identity\SecretId;
use Marvin\Secret\Domain\ValueObject\SecretKey;

final class SecretAlreadyExists extends DomainException implements TranslatableExceptionInterface
{
    public function __construct(
        string $message,
        public readonly ?string $id = null,
        public readonly ?string $key = null,
    ) {
        parent::__construct($message);
    }

    public static function withId(SecretId $id): self
    {
        return new self(
            sprintf('Secret with id "%s" already exists', $id->toString()),
            $id->toString()
        );
    }

    public static function withKey(SecretKey $key): self
    {
        return new self(
            sprintf('Secret with key "%s" already exists', $key->value),
            null,
            $key->value
        );
    }

    public function translationId(): string
    {
        if (null !== $this->id) {
            return 'secret.exceptions.SR0012.secret_already_exists_with_id';
        }

        if (null !== $this->key) {
            return 'secret.exceptions.SR0013.secret_already_exists_with_key';
        }

        return 'secret.exceptions.SR0014.secret_already_exists';
    }

    public function translationParameters(): array
    {
        return [
            '%id%' => $this->id,
            '%key%' => $this->key,
        ];
    }

    public function translationDomain(): string
    {
        return 'secret';
    }
}

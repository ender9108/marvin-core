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

namespace Marvin\Location\Domain\Exception;

use EnderLab\DddCqrsBundle\Domain\Exception\DomainException;
use EnderLab\DddCqrsBundle\Domain\Exception\Interfaces\TranslatableExceptionInterface;
use Marvin\Location\Domain\ValueObject\ZoneName;

final class ZoneAlreadyExists extends DomainException implements TranslatableExceptionInterface
{
    public function __construct(
        string $message,
        public readonly ?string $zoneName = null,
    ) {
        parent::__construct($message);
    }

    public static function withLabel(ZoneName $zoneName): self
    {
        return new self(
            sprintf('Zone with name %s already exists', $zoneName->value),
            $zoneName->value,
        );
    }

    #[Override]
    public function translationId(): string
    {
        if (null !== $this->zoneName) {
            return 'location.exceptions.LO0006.zone_already_exists_with_name';
        }
        return 'location.exceptions.LO0005.zone_already_exists';
    }

    #[Override]
    /** @return array<string, string|null> */
    public function translationParameters(): array
    {
        return [
            '%name%' => $this->zoneName,
        ];
    }

    #[Override]
    public function translationDomain(): string
    {
        return 'location';
    }
}

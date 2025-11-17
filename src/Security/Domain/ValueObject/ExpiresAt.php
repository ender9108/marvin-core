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

namespace Marvin\Security\Domain\ValueObject;

use DateMalformedStringException;
use DateTimeImmutable;
use DateTimeInterface;
use EnderLab\DddCqrsBundle\Domain\Assert\Assert;
use EnderLab\DddCqrsBundle\Domain\ValueObject\ValueObjectTrait;
use Stringable;

final readonly class ExpiresAt implements Stringable
{
    use ValueObjectTrait;

    private const string DATE_FORMAT = 'Y-m-d H:i:s';

    public DateTimeInterface $value;

    public function __construct(DateTimeInterface $expiresAt)
    {
        Assert::dateGreaterThanNow($expiresAt, 'security.exception.SC0036.expires_at_must_be_gt_now');

        $this->value = $expiresAt;
    }

    /**
     * @throws DateMalformedStringException
     */
    public static function fromString(string $expiresAt): self
    {
        return new self(new DateTimeImmutable($expiresAt));
    }

    public function __toString(): string
    {
        return $this->value->format(self::DATE_FORMAT);
    }
}

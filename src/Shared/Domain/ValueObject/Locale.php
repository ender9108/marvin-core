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

namespace Marvin\Shared\Domain\ValueObject;

use EnderLab\DddCqrsBundle\Domain\Assert\Assert;
use Marvin\Shared\Domain\Application;
use Stringable;

final readonly class Locale implements Stringable
{
    private const int LENGTH = 2;

    public string $value;

    public function __construct(string $locale = Application::APP_DEFAULT_LOCALE)
    {
        Assert::notEmpty($locale, 'shared.exceptions.SH0013.locale_does_not_empty');
        Assert::length($locale, self::LENGTH, 'shared.exceptions.SH0014.locale_length');
        Assert::inArray($locale, Application::APP_AVAILABLE_LOCALES, 'shared.exceptions.SH0015.locale_is_not_available');

        $this->value = $locale;
    }

    public static function fromString(string $locale): self
    {
        return new self($locale);
    }

    public function __toString(): string
    {
        return $this->value;
    }

    public static function fr(): self
    {
        return new self(Application::APP_AVAILABLE_LOCALES[0]);
    }

    public static function en(): self
    {
        return new self(Application::APP_AVAILABLE_LOCALES[1]);
    }
}

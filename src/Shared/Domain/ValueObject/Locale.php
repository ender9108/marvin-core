<?php

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
        Assert::notEmpty($locale);
        Assert::length($locale, self::LENGTH);
        Assert::inArray($locale, Application::APP_AVAILABLE_LOCALES);

        $this->value = $locale;
    }

    public function equals(self $other): bool
    {
        return $this->value === $other->value;
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

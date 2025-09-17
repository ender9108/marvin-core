<?php
namespace Marvin\Shared\Domain\ValueObject;

use EnderLab\DddCqrsBundle\Domain\Assert\Assert;
use EnderLab\DddCqrsBundle\Domain\ValueObject\ValueObjectInterface;
use Marvin\Shared\Domain\Application;
use Stringable;

final readonly class Locale implements ValueObjectInterface, Stringable
{
    private const int MIN = 2;
    private const int MAX = 2;

    public string $value;

    public function __construct(string $locale = Application::APP_AVAILABLE_LOCALES[0])
    {
        Assert::notEmpty($locale);
        Assert::lengthBetween($locale, self::MIN, self::MAX);
        Assert::inArray($locale, Application::APP_AVAILABLE_LOCALES);

        $this->value = $locale;
    }

    public function equals(Locale $locale): bool
    {
        return $this->value === $locale->value;
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

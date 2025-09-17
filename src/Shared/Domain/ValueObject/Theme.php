<?php
namespace Marvin\Shared\Domain\ValueObject;

use EnderLab\DddCqrsBundle\Domain\Assert\Assert;
use EnderLab\DddCqrsBundle\Domain\ValueObject\ValueObjectInterface;
use Marvin\Shared\Domain\Application;
use Stringable;

final readonly class Theme implements ValueObjectInterface, Stringable
{
    private const int MAX = 32;

    public string $value;

    public function __construct(string $theme = Application::APP_AVAILABLE_THEMES[0])
    {
        Assert::notEmpty($theme);
        Assert::lengthBetween($theme, 1, self::MAX);
        Assert::inArray($theme, Application::APP_AVAILABLE_THEMES);

        $this->value = $theme;
    }

    public function equals(Theme $theme): bool
    {
        return $this->value === $theme->value;
    }

    public function __toString(): string
    {
        return $this->value;
    }

    public static function dark(): self
    {
        return new self(Application::APP_AVAILABLE_THEMES[0]);
    }

    public static function light(): self
    {
        return new self(Application::APP_AVAILABLE_THEMES[1]);
    }
}

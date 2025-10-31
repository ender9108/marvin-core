<?php

namespace Marvin\Shared\Domain\ValueObject;

use EnderLab\DddCqrsBundle\Domain\Assert\Assert;
use Marvin\Shared\Domain\Application;
use Stringable;

final readonly class Theme implements Stringable
{
    private const int MAX = 32;

    public string $value;

    public function __construct(string $theme = Application::APP_DEFAULT_THEME)
    {
        Assert::notEmpty($theme, 'shared.exceptions.SH0020.theme_does_not_empty');
        Assert::lengthBetween($theme, 1, self::MAX, 'shared.exceptions.SH0021.theme_length_between');
        Assert::inArray($theme, Application::APP_AVAILABLE_THEMES, 'shared.exceptions.SH0022.theme_is_not_available');

        $this->value = $theme;
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

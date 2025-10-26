<?php

namespace Marvin\Secret\Domain\ValueObject;

use EnderLab\DddCqrsBundle\Domain\ValueObject\ValueObjectInterface;
use EnderLab\ToolsBundle\Service\EnumToArrayTrait;

enum SecretCategory: string implements ValueObjectInterface
{
    use EnumToArrayTrait;

    case WIFI = 'wifi';
    case API_KEY = 'api_key';
    case CERTIFICATE = 'certificate';
    case INFRASTRUCTURE = 'infrastructure';

    public function equals(ValueObjectInterface $other): bool
    {
        return $this->value === $other->value;
    }
}

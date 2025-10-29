<?php

namespace Marvin\Secret\Domain\ValueObject;

use EnderLab\ToolsBundle\Service\EnumToArrayTrait;

enum SecretCategory: string
{
    use EnumToArrayTrait;

    case NETWORK = 'network';
    case API_KEY = 'api_key';
    case CERTIFICATE = 'certificate';
    case INFRASTRUCTURE = 'infrastructure';

    public function equals(self $other): bool
    {
        return $this->value === $other->value;
    }
}

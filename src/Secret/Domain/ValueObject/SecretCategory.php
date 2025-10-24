<?php

namespace Marvin\Secret\Domain\ValueObject;

use EnderLab\DddCqrsBundle\Domain\ValueObject\ValueObjectInterface;

enum SecretCategory: string implements ValueObjectInterface
{
    case DATABASE = 'database';
    case MQTT = 'mqtt';
    case WIFI = 'wifi';
    case API_KEY = 'api_key';
    case CERTIFICATE = 'certificate';
    case INFRASTRUCTURE = 'infrastructure';

    public function equals(SecretCategory $secretCategory): bool
    {
        return $this->value === $secretCategory->value;
    }
}

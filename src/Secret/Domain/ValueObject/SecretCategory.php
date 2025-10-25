<?php

namespace Marvin\Secret\Domain\ValueObject;

enum SecretCategory: string
{
    case WIFI = 'wifi';
    case API_KEY = 'api_key';
    case CERTIFICATE = 'certificate';
    case INFRASTRUCTURE = 'infrastructure';
}

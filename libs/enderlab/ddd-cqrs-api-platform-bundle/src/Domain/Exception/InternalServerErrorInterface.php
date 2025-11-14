<?php

declare(strict_types=1);

namespace EnderLab\DddCqrsApiPlatformBundle\Domain\Exception;

interface InternalServerErrorInterface
{
    public const int STATUS_CODE = 500;
}

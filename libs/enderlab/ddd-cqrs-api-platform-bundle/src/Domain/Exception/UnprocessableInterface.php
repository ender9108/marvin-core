<?php

declare(strict_types=1);

namespace EnderLab\DddCqrsApiPlatformBundle\Domain\Exception;

interface UnprocessableInterface
{
    public const int STATUS_CODE = 422;
}

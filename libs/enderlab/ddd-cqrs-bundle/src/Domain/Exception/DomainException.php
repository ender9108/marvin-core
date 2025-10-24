<?php

namespace EnderLab\DddCqrsBundle\Domain\Exception;

use RuntimeException;

abstract class DomainException extends RuntimeException
{
    protected const UNKNOWN_ERROR_CODE = 'E9999';

    protected string $internalCode = self::UNKNOWN_ERROR_CODE;

    public function __construct(string $message, ?string $code = null) {
        parent::__construct($message);

        if (null !== $code) {
            $this->internalCode = $code;
        }
    }

    public function getInternalCode(): string
    {
        return $this->internalCode;

    }
}

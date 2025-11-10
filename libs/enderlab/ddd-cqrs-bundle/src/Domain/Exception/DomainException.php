<?php

namespace EnderLab\DddCqrsBundle\Domain\Exception;

use RuntimeException;

abstract class DomainException extends RuntimeException
{
    protected const string SEPARATOR = '.';

    protected const string UNKNOWN_ERROR_CODE = 'E9999';

    protected string $internalCode = self::UNKNOWN_ERROR_CODE;

    protected ?string $transId = null;

    protected array $transParams = [];

    protected ?string $transDomain = null;

    public function __construct(string $message, string|int|null $code = null) {
        $exceptionCode = 0;

        /*if (
            false !== class_implements($this) &&
            in_array(TranslatableExceptionInterface::class, class_implements($this))
        ) {
            /* Format [DOMAIN_NAME(lowercase)].exceptions.[ERROR_CODE(uppercase)].[TRANSLATION_ID(lower_underscore_case)] */
            /*$translationIdParts = explode(self::SEPARATOR, $message);

            if (count($translationIdParts) >= 4) {
                $this->transDomain = array_shift($translationIdParts);
                // drop "exceptions"
                array_shift($translationIdParts);
                $this->internalCode = array_shift($translationIdParts);
                $this->transId = $message;
            }
        } else {
            if (is_string($code)) {
                $this->internalCode = $code;
            }

            if (is_int($code)) {
                $exceptionCode = $code;
            }
        }*/


        parent::__construct($message, is_int($code) ? $code : $exceptionCode);
    }

    public function getInternalCode(): string
    {
        return $this->internalCode;

    }
}

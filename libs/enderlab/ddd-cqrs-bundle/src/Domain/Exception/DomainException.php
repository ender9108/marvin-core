<?php

namespace EnderLab\DddCqrsBundle\Domain\Exception;

use EnderLab\DddCqrsBundle\Domain\Exception\Interfaces\HttpExceptionInterface;
use EnderLab\DddCqrsBundle\Domain\Exception\Interfaces\TranslatableExceptionInterface;
use RuntimeException;
use Symfony\Component\HttpFoundation\Response;

abstract class DomainException extends RuntimeException implements HttpExceptionInterface
{
    protected const string SEPARATOR = '.';

    protected const string UNKNOWN_ERROR_CODE = 'E9999';

    protected string $internalCode = self::UNKNOWN_ERROR_CODE;

    protected ?string $translationId = null;

    protected array $translationParams = [];

    protected ?string $translationDomain = null;

    public function __construct(
        string $message,
        string|int|null $code = 404
    ) {
        $exceptionCode = 0;

        if (
            false !== class_implements($this) &&
            in_array(TranslatableExceptionInterface::class, class_implements($this))
        ) {
            /* Format [DOMAIN_NAME(lowercase)].exceptions.[ERROR_CODE(uppercase)].[TRANSLATION_ID(lower_underscore_case)] */
            $translationIdParts = explode(self::SEPARATOR, $this->translationId());

            if (count($translationIdParts) >= 4) {
                $this->translationDomain = array_shift($translationIdParts);
                // drop "exceptions"
                array_shift($translationIdParts);
                $this->internalCode = array_shift($translationIdParts);
                $this->translationId = $message;
            }
        } else {
            if (is_string($code)) {
                $this->internalCode = $code;
            }

            if (is_int($code)) {
                $exceptionCode = $code;
            }
        }


        parent::__construct($message, is_int($code) ? $code : $exceptionCode);
    }

    public function getInternalCode(): string
    {
        return $this->internalCode;

    }

    public function getStatusCode(): int
    {
        return Response::HTTP_INTERNAL_SERVER_ERROR;
    }
}

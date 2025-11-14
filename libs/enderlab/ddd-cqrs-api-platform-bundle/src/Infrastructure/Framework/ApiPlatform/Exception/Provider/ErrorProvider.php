<?php

declare(strict_types=1);

namespace EnderLab\DddCqrsApiPlatformBundle\Infrastructure\Framework\ApiPlatform\Exception\Provider;

use ApiPlatform\Metadata\HttpOperation;
use ApiPlatform\Metadata\Operation;
use ApiPlatform\State\ApiResource\Error;
use ApiPlatform\State\ProviderInterface;
use EnderLab\DddCqrsApiPlatformBundle\Domain\Exception\NotFoundInterface;
use EnderLab\DddCqrsApiPlatformBundle\Domain\Exception\UnprocessableInterface;
use EnderLab\DddCqrsBundle\Domain\Exception\Interfaces\HttpExceptionInterface;
use EnderLab\DddCqrsBundle\Domain\Exception\Interfaces\TranslatableExceptionInterface;
use RuntimeException;
use Symfony\Component\DependencyInjection\Attribute\AsAlias;
use Symfony\Component\DependencyInjection\Attribute\AsTaggedItem;
use Symfony\Component\DependencyInjection\ParameterBag\ParameterBagInterface;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Contracts\Translation\TranslatorInterface;

#[AsAlias('api_platform.state.error_provider')]
#[AsTaggedItem('api_platform.state.error_provider')]
final readonly class ErrorProvider implements ProviderInterface
{
    public function __construct(
        private TranslatorInterface $translator,
        private ParameterBagInterface $parameters
    ) {
    }
    public function provide(Operation $operation, array $uriVariables = [], array $context = []): object|array|null
    {
        $request = $context['request'];
        $exception = $request->attributes->get('exception');
        if (!$request || !$exception) {
            throw new RuntimeException();
        }
        /** @var HttpOperation $operation */
        $status = $operation->getStatus() ?? 500;
        $error = Error::createFromException($exception, $status);
        $classImplements = class_implements($exception);
        if (
            false !== $classImplements &&
            in_array(TranslatableExceptionInterface::class, $classImplements) &&
            in_array(HttpExceptionInterface::class, $classImplements)
        ) {
            $documentationUrl = $this->parameters->get('ddd_cqrs_api_platform.documentation_error_link');
            $error->setStatus($this->getStatusCode($exception));
            $error->setDetail(
                $this->translator->trans(
                    $exception->translationId(),
                    $exception->translationParameters(),
                    $exception->translationDomain()
                )
            );
            $error->setType(
                $documentationUrl.'#'.$exception->getInternalCode()
            );
            $error->setTitle($exception->translationId());
        }
        return $error;
    }
    private function getStatusCode(object $exception): int
    {
        $statusCode = match (true) {
            $exception instanceof NotFoundInterface,
            $exception instanceof UnprocessableInterface => $exception::STATUS_CODE,
            default => null
        };
        if (null === $statusCode) {
            if (method_exists($exception, 'getStatusCode')) {
                return $exception->getStatusCode();
            }
            $statusCode = Response::HTTP_INTERNAL_SERVER_ERROR;
        }
        return $statusCode;
    }
}

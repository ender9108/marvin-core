<?php

namespace Marvin\Security\Domain\Exception;

use EnderLab\DddCqrsBundle\Domain\Exception\DomainException;
use EnderLab\DddCqrsBundle\Domain\Exception\TranslatableExceptionInterface;

class ModelNotFound extends DomainException implements TranslatableExceptionInterface
{
    public function __construct(
        private readonly string $model
    ) {
        parent::__construct(sprintf('Model %s not found', $model));
    }

    public function translationId(): string
    {
        return 'security.exceptions.model_not_found';
    }

    public function translationParameters(): array
    {
        return [
            '%model%' => $this->model,
        ];
    }

    public function translationDomain(): string
    {
        return 'security';
    }
}

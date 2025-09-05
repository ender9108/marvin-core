<?php
namespace EnderLab\MarvinManagerBundle\System\Domain\Exception;

use EnderLab\DddCqrsBundle\Domain\Exception\DomainException;
use EnderLab\DddCqrsBundle\Domain\Exception\TranslatableExceptionInterface;
use EnderLab\MarvinManagerBundle\System\Domain\ValueObject\Identity\DockerCustomCommandId;
use Override;

final class DockerCustomCommandNotFound extends DomainException implements TranslatableExceptionInterface
{
    public function __construct(
        ?string $message = null,
        public readonly ?string $id = null,
    ) {
        parent::__construct($message);
    }

    public static function withId(DockerCustomCommandId $id): self
    {
        return new self(
            sprintf('Docker command with id %s was not found', $id->toString()),
            $id->toString(),
        );
    }

    #[Override]
    public function translationId(): string
    {
        return 'security.exceptions.docker_command_not_found';
    }

    #[Override]
    public function translationParameters(): array
    {
        return [
            '%id%' => $this->id,
        ];
    }

    #[Override]
    public function translationDomain(): string
    {
        return 'security';
    }
}

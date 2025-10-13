<?php

namespace Marvin\System\Domain\Exception;

use EnderLab\DddCqrsBundle\Domain\Exception\DomainException;
use EnderLab\DddCqrsBundle\Domain\Exception\TranslatableExceptionInterface;
use Marvin\Security\Domain\ValueObject\Identity\UserId;
use Marvin\Shared\Domain\ValueObject\Email;
use Marvin\System\Domain\ValueObject\Identity\DockerId;
use Override;

final class DockerNotFound extends DomainException implements TranslatableExceptionInterface
{
    public function __construct(
        string $message,
        public readonly ?string $id = null,
    ) {
        parent::__construct($message);
        $this->code = 'SY0002';
    }

    public static function withId(DockerId $dockerId): self
    {
        return new self(
            sprintf('Docker with id %s was not found', $dockerId->toString()),
            $dockerId->toString(),
        );
    }

    #[Override]
    public function translationId(): string
    {
        if (null !== $this->id) {
            return 'system.exceptions.docker_not_found_with_id';
        }

        return 'security.exceptions.docker_not_found';
    }

    #[Override]
    /** @return array<string, string> */
    public function translationParameters(): array
    {
        return [
            '%id%' => $this->id,
        ];
    }

    #[Override]
    public function translationDomain(): string
    {
        return 'system';
    }
}

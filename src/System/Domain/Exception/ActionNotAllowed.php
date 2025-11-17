<?php

/**
 * Marvin Core - DDD-based home automation system
 *
 * @package   Marvin\Core
 * @author    Alexandre Berthelot <alexandreberthelot9108@gmail.com>
 * @copyright 2024-present Alexandre Berthelot
 * @license   AGPL-3.0 License
 * @link      https://github.com/ender9108/marvin-core
 */

declare(strict_types=1);

namespace Marvin\System\Domain\Exception;

use EnderLab\DddCqrsBundle\Domain\Exception\DomainException;
use EnderLab\DddCqrsBundle\Domain\Exception\Interfaces\TranslatableExceptionInterface;
use Marvin\Shared\Domain\ValueObject\Label;
use Marvin\System\Domain\ValueObject\Identity\ContainerId;
use Override;

final class ActionNotAllowed extends DomainException implements TranslatableExceptionInterface
{
    public function __construct(
        string $message,
        public readonly ?string $containerLabel = null,
        public readonly ?string $action = null,
    ) {
        parent::__construct($message);
    }

    public static function withContainerAndAction(Label|ContainerId $container, string $action): self
    {
        return new self(
            sprintf('Action %s not allowed for container %s', $action, $container->value),
            $container->value,
            $action
        );
    }

    #[Override]
    public function translationId(): string
    {
        if (null !== $this->containerLabel && null !== $this->action) {
            return 'system.exceptions.SY0005.action_not_allowed_with_label_and_action';
        }
        return 'system.exceptions.SY0006.action_request_not_allowed';
    }

    #[Override]
    /** @return array<string, string|null> */
    public function translationParameters(): array
    {
        return [
            '%containerLabel%' => $this->containerLabel,
            '%action%' => $this->action,
        ];
    }

    #[Override]
    public function translationDomain(): string
    {
        return 'system';
    }
}

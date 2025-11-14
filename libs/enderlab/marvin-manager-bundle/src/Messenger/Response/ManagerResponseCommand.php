<?php

declare(strict_types=1);

namespace EnderLab\MarvinManagerBundle\Messenger\Response;

use EnderLab\MarvinManagerBundle\Reference\ManagerContainerActionReference;
use EnderLab\MarvinManagerBundle\Reference\ManagerWorkerActionReference;
use Symfony\Component\Validator\Constraints as Assert;
use Symfony\Component\Validator\Constraints\Choice;

final readonly class ManagerResponseCommand implements ManagerResponseCommandInterface
{
    public function __construct(
        #[Assert\NotBlank]
        public string $correlationId,
        #[Assert\NotBlank]
        #[Assert\Choice(choices: ['container', 'worker'])]
        public string $entityType,
        #[Assert\NotBlank]
        public string $entityId,
        #[Assert\NotBlank]
        #[Choice(choices: [
            ManagerContainerActionReference::ACTION_START->value,
            ManagerContainerActionReference::ACTION_STOP->value,
            ManagerContainerActionReference::ACTION_RESTART->value,
            ManagerContainerActionReference::ACTION_RESTART_ALL->value,
            ManagerContainerActionReference::ACTION_BUILD->value,
            ManagerContainerActionReference::ACTION_EXEC_CMD->value,
            ManagerWorkerActionReference::ACTION_START->value,
            ManagerWorkerActionReference::ACTION_STOP->value,
            ManagerWorkerActionReference::ACTION_RESTART->value,
            ManagerWorkerActionReference::ACTION_REREAD->value,
            ManagerWorkerActionReference::ACTION_UPDATE->value,
        ])]
        public string $action,
        public bool $success = false,
        public ?string $output = null,
        public ?string $error = null,
        public ?array $metadata = [],
    ) {
    }
    public function isSuccess(): bool
    {
        return $this->success;
    }
    public function isFailed(): bool
    {
        return !$this->success;
    }
    public function hasError(): bool
    {
        return $this->error !== null;
    }
}

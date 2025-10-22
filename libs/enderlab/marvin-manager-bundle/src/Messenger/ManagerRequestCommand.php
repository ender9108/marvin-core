<?php
namespace EnderLab\MarvinManagerBundle\Messenger;

use EnderLab\DddCqrsBundle\Application\Command\CommandInterface;
use EnderLab\MarvinManagerBundle\Reference\ManagerContainerActionReference;
use EnderLab\MarvinManagerBundle\Reference\ManagerWorkerActionReference;
use Symfony\Component\Validator\Constraints\Choice;
use Symfony\Component\Validator\Constraints\NotBlank;

final readonly class ManagerRequestCommand implements CommandInterface
{
    public function __construct(
        #[NotBlank]
        public string $containerId,
        #[NotBlank]
        public string $correlationId,
        #[NotBlank]
        #[Choice(choices: [
            ManagerContainerActionReference::ACTION_START->value,
            ManagerContainerActionReference::ACTION_START_ALL->value,
            ManagerContainerActionReference::ACTION_STOP->value,
            ManagerContainerActionReference::ACTION_STOP_ALL->value,
            ManagerContainerActionReference::ACTION_RESTART->value,
            ManagerContainerActionReference::ACTION_RESTART_ALL->value,
            ManagerContainerActionReference::ACTION_BUILD->value,
            ManagerContainerActionReference::ACTION_BUILD_ALL->value,
            ManagerContainerActionReference::ACTION_EXEC_CMD->value,
            ManagerWorkerActionReference::ACTION_START->value,
            ManagerWorkerActionReference::ACTION_STOP->value,
            ManagerWorkerActionReference::ACTION_RESTART->value,
            ManagerWorkerActionReference::ACTION_REREAD->value,
            ManagerWorkerActionReference::ACTION_UPDATE->value,
        ])]
        public string $action,
        public ?string $command = null,
        public array $args = [],
        public int $timeout = 10,
    ) {
    }
}

<?php

namespace Marvin\System\Application\Command;

use EnderLab\MarvinManagerBundle\System\Infrastructure\Framework\Symfony\Messenger\Attribute\AsMessageType;
use EnderLab\MarvinManagerBundle\System\Infrastructure\Framework\Symfony\Messenger\ManagerRequestCommand;
use EnderLab\MarvinManagerBundle\System\Infrastructure\List\ManagerMessageReference;
use Symfony\Component\Validator\Constraints as Assert;

#[AsMessageType(binding: ManagerMessageReference::REQUEST_STOP_DOCKER->value)]
class StopDockerRequest extends ManagerRequestCommand
{
    public function __construct(
        #[Assert\Collection(fields: [
            'service' => new Assert\NotNull(),
        ])]
        public array $payload = []
    ) {
        parent::__construct($this->payload);
    }
}

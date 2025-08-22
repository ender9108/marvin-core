<?php

namespace App\System\Application\Command;

use EnderLab\MarvinManagerBundle\List\ManagerMessageReference;
use EnderLab\MarvinManagerBundle\Messenger\Attribute\AsMessageType;
use EnderLab\MarvinManagerBundle\Messenger\ManagerRequestMessage;
use Symfony\Component\Validator\Constraints as Assert;

#[AsMessageType(binding: ManagerMessageReference::REQUEST_STOP_DOCKER)]
class StopDockerRequest extends ManagerRequestMessage
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

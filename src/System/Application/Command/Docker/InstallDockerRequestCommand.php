<?php

namespace App\System\Application\Command\Docker;

use EnderLab\MarvinManagerBundle\List\ManagerMessageReference;
use EnderLab\MarvinManagerBundle\Messenger\Attribute\AsMessageType;
use EnderLab\MarvinManagerBundle\Messenger\ManagerRequestCommand;
use Symfony\Component\Validator\Constraints as Assert;

#[AsMessageType(binding: ManagerMessageReference::REQUEST_INSTALL_DOCKER->value)]
class InstallDockerRequestCommand extends ManagerRequestCommand
{
    public function __construct(
        #[Assert\Collection(fields: [
            'service' => new Assert\NotBlank(),
        ], allowExtraFields: true)]
        public array $payload = []
    ) {
        parent::__construct($this->payload);
    }
}

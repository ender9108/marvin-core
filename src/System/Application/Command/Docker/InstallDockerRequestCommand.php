<?php

namespace App\System\Application\Command\Docker;

use EnderLab\MarvinManagerBundle\System\Infrastructure\List\ManagerMessageReference;
use EnderLab\MarvinManagerBundle\System\Infrastructure\Symfony\Messenger\Attribute\AsMessageType;
use EnderLab\MarvinManagerBundle\System\Infrastructure\Symfony\Messenger\ManagerRequestCommand;
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

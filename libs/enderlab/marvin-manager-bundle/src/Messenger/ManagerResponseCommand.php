<?php

namespace EnderLab\MarvinManagerBundle\Messenger;

use Symfony\Component\Validator\Constraints as Assert;

abstract class ManagerResponseCommand
{
    public function __construct(
        #[Assert\NotBlank]
        public array $payload = []
    ) {
    }
}

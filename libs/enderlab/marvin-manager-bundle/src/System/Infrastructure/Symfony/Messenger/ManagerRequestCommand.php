<?php
namespace EnderLab\MarvinManagerBundle\System\Infrastructure\Symfony\Messenger;

use Symfony\Component\Validator\Constraints as Assert;

abstract class ManagerRequestCommand
{
    public function __construct(
        #[Assert\NotBlank]
        public array $payload = []
    ) {
    }
}

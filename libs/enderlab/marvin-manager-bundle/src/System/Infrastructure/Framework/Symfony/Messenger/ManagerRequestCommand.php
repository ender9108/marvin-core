<?php
namespace EnderLab\MarvinManagerBundle\System\Infrastructure\Framework\Symfony\Messenger;

use Symfony\Component\Validator\Constraints as Assert;

abstract class ManagerRequestCommand
{
    public function __construct(
        #[Assert\NotBlank]
        public array $payload = []
    ) {
    }
}

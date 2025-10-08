<?php

namespace Marvin\Domotic\Application\Command\Device;

use EnderLab\DddCqrsBundle\Application\Command\SyncCommandInterface;
use Marvin\Domotic\Domain\ValueObject\Identity\ProtocolId;
use Marvin\Domotic\Domain\ValueObject\Identity\ZoneId;
use Marvin\Shared\Domain\ValueObject\Label;
use Symfony\Component\Validator\Constraints as Assert;

final readonly class CreateDevice implements SyncCommandInterface
{
    public function __construct(
        public Label $label,
        public ?string $technicalName = null,
        public ?ProtocolId $protocol = null,
        public ?ZoneId $zone = null,
        #[Assert\All(constraints: [new Assert\Uuid(versions: Assert\Uuid::V7_MONOTONIC)])]
        public array $groups = [],
        #[Assert\All(constraints: [new Assert\Uuid(versions: Assert\Uuid::V7_MONOTONIC)])]
        public array $capabilityCompositions = []
    ) {
    }
}

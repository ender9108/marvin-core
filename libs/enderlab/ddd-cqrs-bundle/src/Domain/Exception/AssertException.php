<?php

declare(strict_types=1);

namespace EnderLab\DddCqrsBundle\Domain\Exception;

use Override;

final class AssertException extends InvalidArgument
{
    #[Override]
    public function translationDomain(): string
    {
        return $this->translationDomain;
    }
}

<?php
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

<?php

namespace Marvin\Secret\Application\Query;

use EnderLab\DddCqrsBundle\Application\Query\QueryInterface;
use Marvin\Secret\Domain\ValueObject\SecretKey;

final readonly class GetSecret implements QueryInterface
{
    public function __construct(
        public SecretKey $key,
        public bool $decrypted = false, // Si true, décrypte la valeur
    ) {
    }
}

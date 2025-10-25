<?php

namespace Marvin\Secret\Application\Query;

use Marvin\Secret\Domain\ValueObject\SecretKey;

final readonly class GetSecret
{
    public function __construct(
        public SecretKey $key,
        public bool $decrypted = false, // Si true, décrypte la valeur
    ) {
    }
}

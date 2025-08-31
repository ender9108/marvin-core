<?php

namespace EnderLab\DddCqrsApiPlatformBundle\Service;

use EnderLab\DddCqrsApiPlatformBundle\Iri\Iri;

class IriService
{
    public static function serializeIri(Iri $iri): string
    {
        return $iri->getIri();
    }

    public static function deserializeIri(string $iri): ?Iri
    {
        if (empty($iri)) {
            return null;
        }

        return new Iri($iri);
    }
}

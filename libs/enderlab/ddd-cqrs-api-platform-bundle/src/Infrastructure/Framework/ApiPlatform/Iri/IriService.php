<?php

namespace EnderLab\DddCqrsApiPlatformBundle\Infrastructure\Framework\ApiPlatform\Iri;

class IriService
{
    public static function serializeIri(Iri $iri): string
    {
        return (string) $iri;
    }

    public static function deserializeIri(string $iri): ?Iri
    {
        if (empty($iri)) {
            return null;
        }

        return new Iri($iri);
    }
}

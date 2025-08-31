<?php
namespace EnderLab\DddCqrsApiPlatformBundle\Infrastructure\ApiPlatform\Iri;

use Stringable;

class Iri implements Stringable
{
    private ?string $iri = null;
    private ?string $domain = null;
    private ?string $entity = null;
    private int|string|null $identifier = null;

    public function __construct(?string $iri = null)
    {
        if (!empty($iri)) {
            $this->iri = $iri;
            $this->build($iri);
        }
    }

    public function getIri(): ?string
    {
        return $this->iri;
    }

    public function getDomain(): ?string
    {
        return $this->domain;
    }

    public function getEntity(): ?string
    {
        return $this->entity;
    }

    public function getIdentifier(): ?int
    {
        return $this->identifier;
    }

    public function __toString(): string
    {
        return $this->iri ?? '';
    }

    private function build(string $iri): void
    {
        $parts = explode('/', ltrim($iri, '/'));
        $this->domain = $parts[1];
        $this->entity = $parts[2];
        $this->identifier = (int) $parts[3];
    }

    private static function deserialize(string $iri): Iri
    {
        return new self($iri);
    }

    private static function serialize(Iri $iri): string
    {
        return (string) $iri;
    }
}

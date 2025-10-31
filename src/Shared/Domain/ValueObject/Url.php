<?php

namespace Marvin\Shared\Domain\ValueObject;

use EnderLab\DddCqrsBundle\Domain\Assert\Assert;
use Stringable;

final class Url implements Stringable
{
    private readonly string $scheme;
    private readonly string $host;
    private readonly ?int $port;
    private readonly string $path;
    private readonly ?string $query;
    private readonly ?string $fragment;

    public function __construct(
        public readonly string $value
    ) {
        Assert::notEmpty($this->value, 'L\'URL ne peut pas être vide.');
        Assert::string($this->value, 'L\'URL doit être une chaîne de caractères.');

        $this->validateUrl();

        $parsed = parse_url($this->value);
        Assert::isArray($parsed, 'L\'URL n\'a pas pu être parsée correctement.');

        $this->scheme = $parsed['scheme'] ?? '';
        $this->host = $parsed['host'] ?? '';
        $this->port = $parsed['port'] ?? null;
        $this->path = $parsed['path'] ?? '';
        $this->query = $parsed['query'] ?? null;
        $this->fragment = $parsed['fragment'] ?? null;
    }

    public static function fromString(string $url): self
    {
        return new self($url);
    }

    private function validateUrl(): void
    {
        $pattern = '/^https?:\/\/.+$/i';

        Assert::regex(
            $this->value,
            $pattern,
            'L\'URL doit commencer par http:// ou https:// et contenir un hôte valide.'
        );

        // Validation supplémentaire avec filter_var
        Assert::true(
            filter_var($this->value, FILTER_VALIDATE_URL) !== false,
            'L\'URL n\'est pas valide selon le standard RFC 3986.'
        );
    }

    public function value(): string
    {
        return $this->value;
    }

    public function scheme(): string
    {
        return $this->scheme;
    }

    public function host(): string
    {
        return $this->host;
    }

    public function port(): ?int
    {
        return $this->port;
    }

    public function path(): string
    {
        return $this->path;
    }

    public function query(): ?string
    {
        return $this->query;
    }

    public function fragment(): ?string
    {
        return $this->fragment;
    }

    public function isSecure(): bool
    {
        return strtolower($this->scheme) === 'https';
    }

    public function isHttp(): bool
    {
        return strtolower($this->scheme) === 'http';
    }

    public function getFullHost(): string
    {
        if ($this->port === null) {
            return $this->host;
        }

        // Masquer les ports par défaut
        $defaultPort = $this->isSecure() ? 443 : 80;
        if ($this->port === $defaultPort) {
            return $this->host;
        }

        return sprintf('%s:%d', $this->host, $this->port);
    }

    public function equals(self $other): bool
    {
        return $this->value === $other->value;
    }

    public function __toString(): string
    {
        return $this->value;
    }
}








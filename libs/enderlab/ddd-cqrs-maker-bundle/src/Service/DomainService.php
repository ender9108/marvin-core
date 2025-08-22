<?php

namespace EnderLab\DddCqrsMakerBundle\Service;

use Symfony\Component\DependencyInjection\ParameterBag\ParameterBagInterface;
use Symfony\Component\Finder\Finder;

class DomainService
{
    private array $internalDomainCache = [];
    private array $internalModelCache = [];
    private array $internalModelFqcnCache = [];

    public function __construct(
        private readonly ParameterBagInterface $parameters,
        private ?string $rootPath = null,
        private array $excludePaths = [],
    ) {
        $this->rootPath = $this->parameters->get('kernel.project_dir').'/src/';
        $this->excludePaths = $this->parameters->get('ddd_cqrs_maker.excludes');
    }

    public function getDomains(): array
    {
        if (!empty($this->internalDomainCache)) {
            return $this->internalDomainCache;
        }

        $finder = new Finder();
        $directories = $finder
            ->directories()
            ->ignoreDotFiles(true)
            ->depth(0)
            ->in($this->rootPath)
            ->exclude($this->excludePaths)
        ;

        foreach ($directories as $directory) {
            $dirname = $directory->getBasename();

            if (!in_array($dirname, $this->internalDomainCache)) {
                $this->internalDomainCache[] = $dirname;
            }
        }

        return $this->internalDomainCache;
    }

    public function getModels(?string $domain = null): array
    {
        if (!empty($this->internalModelCache)) {
            return $this->internalModelCache;
        }

        $finder = new Finder();
        $directories = $finder
            ->files()
            ->ignoreDotFiles(true)
            ->depth(0)
            ->in($this->rootPath . ($domain ?? '**').'/Domain/Model/')
            ->exclude($this->excludePaths)
        ;

        foreach ($directories as $directory) {
            $filename = $directory->getBasename('.' . $directory->getExtension());

            if (!in_array($filename, $this->internalModelCache)) {
                $this->internalModelCache[] = $filename;
            }
        }

        return $this->internalModelCache;
    }

    public function checkDomainExist(string $domainName): bool
    {
        if (is_dir($this->rootPath . $domainName)) {
            return true;
        }

        return false;
    }

    public function checkModelExist(string $domainName, string $modelName): bool
    {
        return is_file($this->rootPath . $domainName . '/Domain/Model/' . $modelName . '.php');
    }

    /**
     * Returns all model FQCNs across all domains for autocompletion.
     * Example: App\Domotic\Domain\Model\Test
     *
     * @return string[]
     */
    public function getAllModelFQCNs(): array
    {
        if (!empty($this->internalModelFqcnCache)) {
            return $this->internalModelFqcnCache;
        }

        $finder = new Finder();
        $files = $finder
            ->files()
            ->ignoreDotFiles(true)
            ->in($this->rootPath.'**/Domain/Model')
            ->name('*.php')
            ->exclude($this->excludePaths)
        ;

        foreach ($files as $file) {
            $domain = basename(dirname(dirname($file->getRealPath())));
            $className = $file->getBasename('.'.$file->getExtension());
            $fqcn = sprintf('App\\%s\\Domain\\Model\\%s', $domain, $className);
            if (!in_array($fqcn, $this->internalModelFqcnCache, true)) {
                $this->internalModelFqcnCache[] = $fqcn;
            }
        }

        sort($this->internalModelFqcnCache);

        return $this->internalModelFqcnCache;
    }

    /**
     * Resolve a user-provided class answer to a FQCN.
     * If $answer is already a FQCN of an existing class, it is returned as-is.
     * If it's a short name, first try the preferred domain, else search globally by short name.
     */
    public function resolveModelFqcn(string $answer, ?string $preferredDomain = null): ?string
    {
        $candidate = ltrim($answer, '\\');
        if (str_contains($candidate, '\\')) {
            return class_exists($candidate) ? $candidate : null;
        }

        // Try preferred domain first
        if ($preferredDomain && $this->checkModelExist($preferredDomain, $candidate)) {
            $fqcn = sprintf('App\\%s\\Domain\\Model\\%s', $preferredDomain, $candidate);
            if (class_exists($fqcn)) {
                return $fqcn;
            }
        }

        // Search globally among all model FQCNs
        $short = $candidate;
        $matches = array_filter($this->getAllModelFQCNs(), static function (string $fqcn) use ($short) {
            return str_ends_with($fqcn, '\\'.$short);
        });

        if (count($matches) === 1) {
            return array_values($matches)[0];
        }

        return null; // ambiguous or not found
    }
}

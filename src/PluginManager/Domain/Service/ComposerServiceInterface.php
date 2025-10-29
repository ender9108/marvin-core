<?php

namespace Marvin\PluginManager\Domain\Service;

use Exception;

interface ComposerServiceInterface
{
    /**
     * Met à jour un package via composer
     *
     * @param string $package Nom du package (ex: marvin/viessmann-plugin)
     * @param string|null $version Version cible (null = latest)
     * @param string|null $backupVersion Version de backup pour rollback
     * @return string Output de composer
     * @throws Exception En cas d'erreur
     */
    public function update(string $package, ?string $version = null, ?string $backupVersion = null): string;

    /**
     * Force une version spécifique d'un package
     *
     * @param string $package Nom du package
     * @param string $version Version à installer
     * @return string Output de composer
     * @throws Exception En cas d'erreur
     */
    public function requireVersion(string $package, string $version): string;
}

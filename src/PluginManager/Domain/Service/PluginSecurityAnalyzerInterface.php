<?php

namespace Marvin\PluginManager\Domain\Service;

use Marvin\PluginManager\Domain\Model\SecurityReport;

interface PluginSecurityAnalyzerInterface
{
    /**
     * Analyse la sécurité d'un plugin
     *
     * @param string $pluginClass Classe du plugin à analyser
     * @return SecurityReport Rapport de sécurité
     */
    public function analyze(string $pluginClass): SecurityReport;
}

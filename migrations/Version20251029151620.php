<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20251029151620 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('CREATE TABLE plugin_manager_plugin (id UUID NOT NULL, class VARCHAR(255) NOT NULL, vendor VARCHAR(255) NOT NULL, package VARCHAR(255) NOT NULL, status VARCHAR(16) NOT NULL, author VARCHAR(255) NOT NULL, homepage VARCHAR(255) NOT NULL, capabilities JSON NOT NULL, installed_at TIMESTAMP(0) WITHOUT TIME ZONE NOT NULL, enabled_at TIMESTAMP(0) WITHOUT TIME ZONE DEFAULT NULL, disabled_at TIMESTAMP(0) WITHOUT TIME ZONE DEFAULT NULL, last_analyzed_at TIMESTAMP(0) WITHOUT TIME ZONE DEFAULT NULL, blocked_at TIMESTAMP(0) WITHOUT TIME ZONE DEFAULT NULL, blocked_reason VARCHAR(255) DEFAULT NULL, name_value VARCHAR(100) NOT NULL, slug_value VARCHAR(128) NOT NULL, version_value VARCHAR(16) NOT NULL, description_value TEXT DEFAULT NULL, metadata_value JSON DEFAULT NULL, PRIMARY KEY(id))');
        $this->addSql('COMMENT ON COLUMN plugin_manager_plugin.id IS \'(DC2Type:plugin_id)\'');

        $this->addSql('CREATE TABLE plugin_manager_security_report (id UUID NOT NULL, plugin_id UUID DEFAULT NULL, status VARCHAR(16) NOT NULL, analyzed_at TIMESTAMP(0) WITHOUT TIME ZONE NOT NULL, analyzer_version VARCHAR(20) NOT NULL, violations JSON NOT NULL, summary JSON NOT NULL, PRIMARY KEY(id, analyzed_at))');

        // Index pour les requêtes sur plugin_id
        $this->addSql('CREATE INDEX idx_security_report_plugin ON plugin_manager_security_report (plugin_id)');

        $this->addSql('COMMENT ON COLUMN plugin_manager_security_report.id IS \'(DC2Type:security_report_id)\'');
        $this->addSql('COMMENT ON COLUMN plugin_manager_security_report.plugin_id IS \'(DC2Type:plugin_id)\'');

        // Clé étrangère
        $this->addSql('ALTER TABLE plugin_manager_security_report ADD CONSTRAINT FK_9654DA2AEC942BCF FOREIGN KEY (plugin_id) REFERENCES plugin_manager_plugin (id) ON DELETE CASCADE NOT DEFERRABLE INITIALLY IMMEDIATE');

        // Indexes sur la table plugin
        $this->addSql('CREATE INDEX idx_plugin_status ON plugin_manager_plugin (status)');
        $this->addSql('CREATE INDEX idx_plugin_vendor ON plugin_manager_plugin (vendor)');

        // Convertir en hypertable TimescaleDB
        // IMPORTANT: analyzed_at doit être dans un index unique avant create_hypertable
        $this->addSql("SELECT create_hypertable('plugin_manager_security_report', 'analyzed_at', if_not_exists => true)");

        // Politique de rétention (1 an)
        $this->addSql("SELECT add_retention_policy('plugin_manager_security_report', INTERVAL '1 year', if_not_exists => true)");
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql("SELECT drop_hypertable('plugin_manager_security_report', if_exists => true)");

        $this->addSql('ALTER TABLE plugin_manager_security_report DROP CONSTRAINT FK_9654DA2AEC942BCF');
        $this->addSql('DROP TABLE plugin_manager_security_report');
        $this->addSql('DROP TABLE plugin_manager_plugin');
    }
}

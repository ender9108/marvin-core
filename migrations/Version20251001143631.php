<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20251001143631 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('CREATE TABLE system_plugin (id UUID NOT NULL, status_id UUID DEFAULT NULL, label_value VARCHAR(255) NOT NULL, reference_value VARCHAR(64) NOT NULL, description_value TEXT DEFAULT NULL, version_value VARCHAR(8) NOT NULL, PRIMARY KEY(id))');
        $this->addSql('CREATE UNIQUE INDEX UNIQ_F9E09255CBD63824 ON system_plugin (reference_value)');
        $this->addSql('CREATE INDEX IDX_F9E092556BF700BD ON system_plugin (status_id)');
        $this->addSql('COMMENT ON COLUMN system_plugin.id IS \'(DC2Type:plugin_id)\'');
        $this->addSql('COMMENT ON COLUMN system_plugin.status_id IS \'(DC2Type:plugin_status_id)\'');
        $this->addSql('ALTER TABLE system_plugin ADD CONSTRAINT FK_F9E092556BF700BD FOREIGN KEY (status_id) REFERENCES system_plugin_status (id) NOT DEFERRABLE INITIALLY IMMEDIATE');
        $this->addSql('ALTER TABLE security_user ALTER email TYPE VARCHAR(500)');
        $this->addSql('ALTER TABLE security_user ALTER email TYPE VARCHAR(500)');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('CREATE SCHEMA public');
        $this->addSql('ALTER TABLE system_plugin DROP CONSTRAINT FK_F9E092556BF700BD');
        $this->addSql('DROP TABLE system_plugin');
        $this->addSql('ALTER TABLE security_user ALTER email TYPE VARCHAR(500)');
    }
}

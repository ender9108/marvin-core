<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20251008130025 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('CREATE TABLE device_capability_composition (device_id UUID NOT NULL, capability_composition_id UUID NOT NULL, PRIMARY KEY(device_id, capability_composition_id))');
        $this->addSql('CREATE INDEX IDX_90CA50CB94A4C7D4 ON device_capability_composition (device_id)');
        $this->addSql('CREATE INDEX IDX_90CA50CBF03406B ON device_capability_composition (capability_composition_id)');
        $this->addSql('COMMENT ON COLUMN device_capability_composition.device_id IS \'(DC2Type:device_id)\'');
        $this->addSql('COMMENT ON COLUMN device_capability_composition.capability_composition_id IS \'(DC2Type:capability_composition_id)\'');
        $this->addSql('ALTER TABLE device_capability_composition ADD CONSTRAINT FK_90CA50CB94A4C7D4 FOREIGN KEY (device_id) REFERENCES domotic_device (id) ON DELETE CASCADE NOT DEFERRABLE INITIALLY IMMEDIATE');
        $this->addSql('ALTER TABLE device_capability_composition ADD CONSTRAINT FK_90CA50CBF03406B FOREIGN KEY (capability_composition_id) REFERENCES domotic_capability_composition (id) ON DELETE CASCADE NOT DEFERRABLE INITIALLY IMMEDIATE');
        $this->addSql('ALTER TABLE security_user ALTER email TYPE VARCHAR(500)');
        $this->addSql('ALTER TABLE security_user ALTER email TYPE VARCHAR(500)');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('CREATE SCHEMA public');
        $this->addSql('CREATE SCHEMA timescaledb_information');
        $this->addSql('CREATE SCHEMA timescaledb_experimental');
        $this->addSql('CREATE SCHEMA _timescaledb_internal');
        $this->addSql('CREATE SCHEMA _timescaledb_functions');
        $this->addSql('CREATE SCHEMA _timescaledb_debug');
        $this->addSql('CREATE SCHEMA _timescaledb_config');
        $this->addSql('CREATE SCHEMA _timescaledb_catalog');
        $this->addSql('CREATE SCHEMA _timescaledb_cache');
        $this->addSql('ALTER TABLE device_capability_composition DROP CONSTRAINT FK_90CA50CB94A4C7D4');
        $this->addSql('ALTER TABLE device_capability_composition DROP CONSTRAINT FK_90CA50CBF03406B');
        $this->addSql('DROP TABLE device_capability_composition');
        $this->addSql('ALTER TABLE security_user ALTER email TYPE VARCHAR(500)');
    }
}

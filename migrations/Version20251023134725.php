<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20251023134725 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE location_zone ADD parent_id UUID DEFAULT NULL');
        $this->addSql('ALTER TABLE location_zone DROP parent_zone_id');
        $this->addSql('COMMENT ON COLUMN location_zone.parent_id IS \'(DC2Type:zone_id)\'');
        $this->addSql('ALTER TABLE location_zone ADD CONSTRAINT FK_20EF7547727ACA70 FOREIGN KEY (parent_id) REFERENCES location_zone (id) NOT DEFERRABLE INITIALLY IMMEDIATE');
        $this->addSql('CREATE INDEX IDX_20EF7547727ACA70 ON location_zone (parent_id)');
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
        $this->addSql('ALTER TABLE security_user ALTER email TYPE VARCHAR(500)');
        $this->addSql('ALTER TABLE location_zone DROP CONSTRAINT FK_20EF7547727ACA70');
        $this->addSql('DROP INDEX IDX_20EF7547727ACA70');
        $this->addSql('ALTER TABLE location_zone ADD parent_zone_id VARCHAR(32) NOT NULL');
        $this->addSql('ALTER TABLE location_zone DROP parent_id');
    }
}

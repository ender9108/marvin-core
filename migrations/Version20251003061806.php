<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20251003061806 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('CREATE TABLE domotic_protocol_status (id UUID NOT NULL, label_value VARCHAR(255) NOT NULL, reference_value VARCHAR(64) NOT NULL, PRIMARY KEY(id))');
        $this->addSql('CREATE UNIQUE INDEX UNIQ_7AF6E389CBD63824 ON domotic_protocol_status (reference_value)');
        $this->addSql('COMMENT ON COLUMN domotic_protocol_status.id IS \'(DC2Type:protocol_status_id)\'');
        $this->addSql('CREATE TABLE domotic_zone (id UUID NOT NULL, parentzone_id UUID DEFAULT NULL, label_value VARCHAR(255) NOT NULL, area_value DOUBLE PRECISION DEFAULT \'0.0\' NOT NULL, PRIMARY KEY(id))');
        $this->addSql('CREATE INDEX IDX_B7D69EFA290C1BEE ON domotic_zone (parentzone_id)');
        $this->addSql('COMMENT ON COLUMN domotic_zone.id IS \'(DC2Type:zone_id)\'');
        $this->addSql('COMMENT ON COLUMN domotic_zone.parentzone_id IS \'(DC2Type:zone_id)\'');
        $this->addSql('ALTER TABLE domotic_zone ADD CONSTRAINT FK_B7D69EFA290C1BEE FOREIGN KEY (parentzone_id) REFERENCES domotic_zone (id) NOT DEFERRABLE INITIALLY IMMEDIATE');
        $this->addSql('ALTER TABLE security_user ALTER email TYPE VARCHAR(500)');
        $this->addSql('ALTER TABLE security_user ALTER email TYPE VARCHAR(500)');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('CREATE SCHEMA public');
        $this->addSql('ALTER TABLE domotic_zone DROP CONSTRAINT FK_B7D69EFA290C1BEE');
        $this->addSql('DROP TABLE domotic_protocol_status');
        $this->addSql('DROP TABLE domotic_zone');
        $this->addSql('ALTER TABLE security_user ALTER email TYPE VARCHAR(500)');
    }
}

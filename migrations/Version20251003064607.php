<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20251003064607 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('CREATE TABLE domotic_protocol (id UUID NOT NULL, status_id UUID NOT NULL, label_value VARCHAR(255) NOT NULL, reference_value VARCHAR(64) NOT NULL, description_value TEXT DEFAULT NULL, metadata_value JSON NOT NULL, PRIMARY KEY(id))');
        $this->addSql('CREATE UNIQUE INDEX UNIQ_33D5BCF5CBD63824 ON domotic_protocol (reference_value)');
        $this->addSql('CREATE INDEX IDX_33D5BCF56BF700BD ON domotic_protocol (status_id)');
        $this->addSql('COMMENT ON COLUMN domotic_protocol.id IS \'(DC2Type:protocol_id)\'');
        $this->addSql('COMMENT ON COLUMN domotic_protocol.status_id IS \'(DC2Type:protocol_status_id)\'');
        $this->addSql('ALTER TABLE domotic_protocol ADD CONSTRAINT FK_33D5BCF56BF700BD FOREIGN KEY (status_id) REFERENCES domotic_protocol_status (id) NOT DEFERRABLE INITIALLY IMMEDIATE');
        $this->addSql('ALTER TABLE security_user ALTER email TYPE VARCHAR(500)');
        $this->addSql('ALTER TABLE security_user ALTER email TYPE VARCHAR(500)');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('CREATE SCHEMA public');
        $this->addSql('ALTER TABLE domotic_protocol DROP CONSTRAINT FK_33D5BCF56BF700BD');
        $this->addSql('DROP TABLE domotic_protocol');
        $this->addSql('ALTER TABLE security_user ALTER email TYPE VARCHAR(500)');
    }
}

<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20250828105840 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE device ADD protocol_id UUID NOT NULL');
        $this->addSql('COMMENT ON COLUMN device.protocol_id IS \'(DC2Type:uuid)\'');
        $this->addSql('ALTER TABLE device ADD CONSTRAINT FK_92FB68ECCD59258 FOREIGN KEY (protocol_id) REFERENCES protocol (id) NOT DEFERRABLE INITIALLY IMMEDIATE');
        $this->addSql('CREATE INDEX IDX_92FB68ECCD59258 ON device (protocol_id)');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('CREATE SCHEMA public');
        $this->addSql('ALTER TABLE device DROP CONSTRAINT FK_92FB68ECCD59258');
        $this->addSql('DROP INDEX IDX_92FB68ECCD59258');
        $this->addSql('ALTER TABLE device DROP protocol_id');
    }
}

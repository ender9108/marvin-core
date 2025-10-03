<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20251003115644 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('CREATE TABLE domotic_capability (id UUID NOT NULL, label_value VARCHAR(255) NOT NULL, reference_value VARCHAR(64) NOT NULL, PRIMARY KEY(id))');
        $this->addSql('CREATE UNIQUE INDEX UNIQ_A3B60CD4CBD63824 ON domotic_capability (reference_value)');
        $this->addSql('COMMENT ON COLUMN domotic_capability.id IS \'(DC2Type:capability_id)\'');
        $this->addSql('CREATE TABLE domotic_capability_action (id UUID NOT NULL, label_value VARCHAR(255) NOT NULL, reference_value VARCHAR(64) NOT NULL, PRIMARY KEY(id))');
        $this->addSql('CREATE UNIQUE INDEX UNIQ_56E8C763CBD63824 ON domotic_capability_action (reference_value)');
        $this->addSql('COMMENT ON COLUMN domotic_capability_action.id IS \'(DC2Type:capability_action_id)\'');
        $this->addSql('CREATE TABLE domotic_capability_composition (id UUID NOT NULL, capability_id UUID DEFAULT NULL, PRIMARY KEY(id))');
        $this->addSql('CREATE INDEX IDX_59A328BA92043242 ON domotic_capability_composition (capability_id)');
        $this->addSql('COMMENT ON COLUMN domotic_capability_composition.id IS \'(DC2Type:capability_composition_id)\'');
        $this->addSql('COMMENT ON COLUMN domotic_capability_composition.capability_id IS \'(DC2Type:capability_id)\'');
        $this->addSql('CREATE TABLE domotic_capability_state (id UUID NOT NULL, stateschema JSON NOT NULL, label_value VARCHAR(255) NOT NULL, reference_value VARCHAR(64) NOT NULL, PRIMARY KEY(id))');
        $this->addSql('CREATE UNIQUE INDEX UNIQ_86D37F20CBD63824 ON domotic_capability_state (reference_value)');
        $this->addSql('COMMENT ON COLUMN domotic_capability_state.id IS \'(DC2Type:capability_state_id)\'');
        $this->addSql('ALTER TABLE domotic_capability_composition ADD CONSTRAINT FK_59A328BA92043242 FOREIGN KEY (capability_id) REFERENCES domotic_capability (id) NOT DEFERRABLE INITIALLY IMMEDIATE');
        $this->addSql('ALTER TABLE security_user ALTER email TYPE VARCHAR(500)');
        $this->addSql('ALTER TABLE security_user ALTER email TYPE VARCHAR(500)');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('CREATE SCHEMA public');
        $this->addSql('ALTER TABLE domotic_capability_composition DROP CONSTRAINT FK_59A328BA92043242');
        $this->addSql('DROP TABLE domotic_capability');
        $this->addSql('DROP TABLE domotic_capability_action');
        $this->addSql('DROP TABLE domotic_capability_composition');
        $this->addSql('DROP TABLE domotic_capability_state');
        $this->addSql('ALTER TABLE security_user ALTER email TYPE VARCHAR(500)');
    }
}

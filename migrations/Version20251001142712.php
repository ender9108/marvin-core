<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20251001142712 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('CREATE TABLE system_plugin_status (id UUID NOT NULL, label_value VARCHAR(255) NOT NULL, reference_value VARCHAR(64) NOT NULL, PRIMARY KEY(id))');
        $this->addSql('CREATE UNIQUE INDEX UNIQ_70CA6D35CBD63824 ON system_plugin_status (reference_value)');
        $this->addSql('COMMENT ON COLUMN system_plugin_status.id IS \'(DC2Type:plugin_status_id)\'');
        $this->addSql('ALTER TABLE security_user ALTER email TYPE VARCHAR(500)');
        $this->addSql('ALTER TABLE security_user ALTER email TYPE VARCHAR(500)');
        $this->addSql('CREATE UNIQUE INDEX UNIQ_3EFD2940CBD63824 ON security_user_status (reference_value)');
        $this->addSql('CREATE UNIQUE INDEX UNIQ_9EE1C39FCBD63824 ON security_user_type (reference_value)');
        $this->addSql('CREATE UNIQUE INDEX UNIQ_B750EC39CBD63824 ON system_docker_command (reference_value)');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('CREATE SCHEMA public');
        $this->addSql('DROP TABLE system_plugin_status');
        $this->addSql('DROP INDEX UNIQ_3EFD2940CBD63824');
        $this->addSql('DROP INDEX UNIQ_B750EC39CBD63824');
        $this->addSql('ALTER TABLE security_user ALTER email TYPE VARCHAR(500)');
        $this->addSql('DROP INDEX UNIQ_9EE1C39FCBD63824');
    }
}

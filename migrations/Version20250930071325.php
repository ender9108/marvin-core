<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20250930071325 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('CREATE TABLE system_docker (id UUID NOT NULL, container_id VARCHAR(128) DEFAULT NULL, container_name VARCHAR(255) DEFAULT NULL, container_image VARCHAR(255) DEFAULT NULL, container_service VARCHAR(255) DEFAULT NULL, container_state VARCHAR(64) DEFAULT NULL, container_status VARCHAR(255) DEFAULT NULL, container_project VARCHAR(255) DEFAULT NULL, definition JSON NOT NULL, created_at_value TIMESTAMP(0) WITHOUT TIME ZONE NOT NULL, updated_at_value TIMESTAMP(0) WITHOUT TIME ZONE DEFAULT NULL, PRIMARY KEY(id))');
        $this->addSql('COMMENT ON COLUMN system_docker.id IS \'(DC2Type:docker_id)\'');
        $this->addSql('CREATE TABLE system_docker_command (id UUID NOT NULL, docker_id UUID DEFAULT NULL, reference_value VARCHAR(64) NOT NULL, command_value TEXT NOT NULL, created_at_value TIMESTAMP(0) WITHOUT TIME ZONE NOT NULL, updated_at_value TIMESTAMP(0) WITHOUT TIME ZONE DEFAULT NULL, PRIMARY KEY(id))');
        $this->addSql('CREATE INDEX IDX_B750EC393FC453F8 ON system_docker_command (docker_id)');
        $this->addSql('COMMENT ON COLUMN system_docker_command.id IS \'(DC2Type:docker_custom_command_id)\'');
        $this->addSql('COMMENT ON COLUMN system_docker_command.docker_id IS \'(DC2Type:docker_id)\'');
        $this->addSql('ALTER TABLE system_docker_command ADD CONSTRAINT FK_B750EC393FC453F8 FOREIGN KEY (docker_id) REFERENCES system_docker (id) ON DELETE SET NULL NOT DEFERRABLE INITIALLY IMMEDIATE');
        $this->addSql('ALTER TABLE security_user ALTER email TYPE VARCHAR(500)');
        $this->addSql('ALTER TABLE security_user ALTER email TYPE VARCHAR(500)');
        $this->addSql('ALTER TABLE security_user ALTER locale_value TYPE VARCHAR(2)');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('CREATE SCHEMA public');
        $this->addSql('ALTER TABLE system_docker_command DROP CONSTRAINT FK_B750EC393FC453F8');
        $this->addSql('DROP TABLE system_docker');
        $this->addSql('DROP TABLE system_docker_command');
        $this->addSql('ALTER TABLE security_user ALTER email TYPE VARCHAR(500)');
        $this->addSql('ALTER TABLE security_user ALTER locale_value TYPE VARCHAR(255)');
    }
}

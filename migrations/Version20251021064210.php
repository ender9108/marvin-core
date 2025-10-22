<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20251021064210 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE security_login_attempt RENAME COLUMN created_at_value TO created_at');
        $this->addSql('ALTER TABLE security_request_reset_password RENAME COLUMN created_at_value TO created_at');
        $this->addSql('ALTER TABLE security_user ALTER email TYPE VARCHAR(500)');
        $this->addSql('ALTER TABLE security_user ALTER email TYPE VARCHAR(500)');
        $this->addSql('ALTER TABLE security_user RENAME COLUMN updated_at_value TO updated_at');
        $this->addSql('ALTER TABLE security_user RENAME COLUMN created_at_value TO created_at');
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
        $this->addSql('ALTER TABLE security_request_reset_password RENAME COLUMN created_at TO created_at_value');
        $this->addSql('ALTER TABLE security_user ALTER email TYPE VARCHAR(500)');
        $this->addSql('ALTER TABLE security_user RENAME COLUMN created_at TO created_at_value');
        $this->addSql('ALTER TABLE security_user RENAME COLUMN updated_at TO updated_at_value');
        $this->addSql('ALTER TABLE security_login_attempt RENAME COLUMN created_at TO created_at_value');
    }
}

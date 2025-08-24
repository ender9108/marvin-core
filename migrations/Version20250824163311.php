<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20250824163311 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('CREATE TABLE capability_state_schema (id INT UNSIGNED AUTO_INCREMENT NOT NULL, label VARCHAR(255) NOT NULL, reference VARCHAR(128) NOT NULL, aggregate_id VARCHAR(255) NOT NULL, created_by VARCHAR(255) DEFAULT NULL, updated_by VARCHAR(255) DEFAULT NULL, created_at DATETIME NOT NULL, updated_at DATETIME DEFAULT NULL, UNIQUE INDEX UNIQ_1A76D5E1D0BBCCBE (aggregate_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE capability_state_schema_capability_action (capability_state_schema_id INT UNSIGNED NOT NULL, capability_action_id SMALLINT UNSIGNED NOT NULL, INDEX IDX_3964268F8B489AEE (capability_state_schema_id), INDEX IDX_3964268F3AD9EF42 (capability_action_id), PRIMARY KEY(capability_state_schema_id, capability_action_id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE device (id VARCHAR(255) NOT NULL, name VARCHAR(255) NOT NULL, technical_name VARCHAR(255) NOT NULL, aggregate_id VARCHAR(255) NOT NULL, created_by VARCHAR(255) DEFAULT NULL, updated_by VARCHAR(255) DEFAULT NULL, created_at DATETIME NOT NULL, updated_at DATETIME DEFAULT NULL, UNIQUE INDEX UNIQ_92FB68ED0BBCCBE (aggregate_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE device_capability (device_id VARCHAR(255) NOT NULL, capability_id INT UNSIGNED NOT NULL, INDEX IDX_1182A1F694A4C7D4 (device_id), INDEX IDX_1182A1F692043242 (capability_id), PRIMARY KEY(device_id, capability_id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE `group` (id INT UNSIGNED AUTO_INCREMENT NOT NULL, name VARCHAR(255) NOT NULL, slug VARCHAR(255) NOT NULL, created_by VARCHAR(255) DEFAULT NULL, updated_by VARCHAR(255) DEFAULT NULL, created_at DATETIME NOT NULL, updated_at DATETIME DEFAULT NULL, PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE group_device (group_id INT UNSIGNED NOT NULL, device_id VARCHAR(255) NOT NULL, INDEX IDX_DAA96E5EFE54D947 (group_id), INDEX IDX_DAA96E5E94A4C7D4 (device_id), PRIMARY KEY(group_id, device_id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('ALTER TABLE capability_state_schema_capability_action ADD CONSTRAINT FK_3964268F8B489AEE FOREIGN KEY (capability_state_schema_id) REFERENCES capability_state_schema (id) ON DELETE CASCADE');
        $this->addSql('ALTER TABLE capability_state_schema_capability_action ADD CONSTRAINT FK_3964268F3AD9EF42 FOREIGN KEY (capability_action_id) REFERENCES capability_action (id) ON DELETE CASCADE');
        $this->addSql('ALTER TABLE device_capability ADD CONSTRAINT FK_1182A1F694A4C7D4 FOREIGN KEY (device_id) REFERENCES device (id) ON DELETE CASCADE');
        $this->addSql('ALTER TABLE device_capability ADD CONSTRAINT FK_1182A1F692043242 FOREIGN KEY (capability_id) REFERENCES capability (id) ON DELETE CASCADE');
        $this->addSql('ALTER TABLE group_device ADD CONSTRAINT FK_DAA96E5EFE54D947 FOREIGN KEY (group_id) REFERENCES `group` (id) ON DELETE CASCADE');
        $this->addSql('ALTER TABLE group_device ADD CONSTRAINT FK_DAA96E5E94A4C7D4 FOREIGN KEY (device_id) REFERENCES device (id) ON DELETE CASCADE');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE capability_state_schema_capability_action DROP FOREIGN KEY FK_3964268F8B489AEE');
        $this->addSql('ALTER TABLE capability_state_schema_capability_action DROP FOREIGN KEY FK_3964268F3AD9EF42');
        $this->addSql('ALTER TABLE device_capability DROP FOREIGN KEY FK_1182A1F694A4C7D4');
        $this->addSql('ALTER TABLE device_capability DROP FOREIGN KEY FK_1182A1F692043242');
        $this->addSql('ALTER TABLE group_device DROP FOREIGN KEY FK_DAA96E5EFE54D947');
        $this->addSql('ALTER TABLE group_device DROP FOREIGN KEY FK_DAA96E5E94A4C7D4');
        $this->addSql('DROP TABLE capability_state_schema');
        $this->addSql('DROP TABLE capability_state_schema_capability_action');
        $this->addSql('DROP TABLE device');
        $this->addSql('DROP TABLE device_capability');
        $this->addSql('DROP TABLE `group`');
        $this->addSql('DROP TABLE group_device');
    }
}

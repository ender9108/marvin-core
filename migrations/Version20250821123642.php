<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20250821123642 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('CREATE TABLE capability_capability_action (capability_id INT UNSIGNED NOT NULL, capability_action_id SMALLINT UNSIGNED NOT NULL, INDEX IDX_711B377F92043242 (capability_id), INDEX IDX_711B377F3AD9EF42 (capability_action_id), PRIMARY KEY(capability_id, capability_action_id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('ALTER TABLE capability_capability_action ADD CONSTRAINT FK_711B377F92043242 FOREIGN KEY (capability_id) REFERENCES capability (id) ON DELETE CASCADE');
        $this->addSql('ALTER TABLE capability_capability_action ADD CONSTRAINT FK_711B377F3AD9EF42 FOREIGN KEY (capability_action_id) REFERENCES capability_action (id) ON DELETE CASCADE');
        $this->addSql('DROP TABLE tag');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('CREATE TABLE tag (id INT AUTO_INCREMENT NOT NULL, label VARCHAR(255) CHARACTER SET utf8mb4 NOT NULL COLLATE `utf8mb4_unicode_ci`, datas JSON NOT NULL COMMENT \'(DC2Type:json)\', created_at DATETIME NOT NULL, updated_at DATETIME DEFAULT NULL, created_by VARCHAR(255) CHARACTER SET utf8mb4 DEFAULT NULL COLLATE `utf8mb4_unicode_ci`, updated_by VARCHAR(255) CHARACTER SET utf8mb4 DEFAULT NULL COLLATE `utf8mb4_unicode_ci`, PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB COMMENT = \'\' ');
        $this->addSql('ALTER TABLE capability_capability_action DROP FOREIGN KEY FK_711B377F92043242');
        $this->addSql('ALTER TABLE capability_capability_action DROP FOREIGN KEY FK_711B377F3AD9EF42');
        $this->addSql('DROP TABLE capability_capability_action');
    }
}

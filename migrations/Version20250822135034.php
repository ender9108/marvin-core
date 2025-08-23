<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20250822135034 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('CREATE TABLE protocol (id INT UNSIGNED AUTO_INCREMENT NOT NULL, status_id INT UNSIGNED NOT NULL, label VARCHAR(128) NOT NULL, reference VARCHAR(64) NOT NULL, description LONGTEXT DEFAULT NULL, created_by VARCHAR(255) DEFAULT NULL, updated_by VARCHAR(255) DEFAULT NULL, created_at DATETIME NOT NULL, updated_at DATETIME DEFAULT NULL, INDEX IDX_C8C0BC4C6BF700BD (status_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE protocol_status (id INT UNSIGNED AUTO_INCREMENT NOT NULL, label VARCHAR(128) NOT NULL, reference VARCHAR(64) NOT NULL, created_by VARCHAR(255) DEFAULT NULL, updated_by VARCHAR(255) DEFAULT NULL, created_at DATETIME NOT NULL, updated_at DATETIME DEFAULT NULL, PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('ALTER TABLE protocol ADD CONSTRAINT FK_C8C0BC4C6BF700BD FOREIGN KEY (status_id) REFERENCES protocol_status (id)');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE protocol DROP FOREIGN KEY FK_C8C0BC4C6BF700BD');
        $this->addSql('DROP TABLE protocol');
        $this->addSql('DROP TABLE protocol_status');
    }
}

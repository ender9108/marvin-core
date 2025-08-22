<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20250820103533 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('CREATE TABLE capability (id INT UNSIGNED AUTO_INCREMENT NOT NULL, label VARCHAR(255) NOT NULL, reference VARCHAR(128) NOT NULL, aggregate_id VARCHAR(255) NOT NULL, created_by VARCHAR(255) DEFAULT NULL, updated_by VARCHAR(255) DEFAULT NULL, created_at DATETIME NOT NULL, updated_at DATETIME DEFAULT NULL, UNIQUE INDEX UNIQ_96B1E230D0BBCCBE (aggregate_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE capability_action (id SMALLINT UNSIGNED AUTO_INCREMENT NOT NULL, label VARCHAR(255) NOT NULL, reference VARCHAR(128) NOT NULL, created_by VARCHAR(255) DEFAULT NULL, updated_by VARCHAR(255) DEFAULT NULL, created_at DATETIME NOT NULL, updated_at DATETIME DEFAULT NULL, PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE docker (id INT AUTO_INCREMENT NOT NULL, container_id VARCHAR(255) NOT NULL, container_name VARCHAR(255) NOT NULL, container_image VARCHAR(255) DEFAULT NULL, container_service VARCHAR(128) DEFAULT NULL, container_state VARCHAR(128) DEFAULT NULL, container_status VARCHAR(255) DEFAULT NULL, container_project VARCHAR(255) DEFAULT NULL, definition JSON NOT NULL COMMENT \'(DC2Type:json)\', created_at DATETIME NOT NULL, updated_at DATETIME DEFAULT NULL, created_by VARCHAR(255) DEFAULT NULL, updated_by VARCHAR(255) DEFAULT NULL, PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE docker_custom_command (id INT AUTO_INCREMENT NOT NULL, docker_id INT NOT NULL, reference VARCHAR(64) NOT NULL, command LONGTEXT NOT NULL, created_at DATETIME NOT NULL, updated_at DATETIME DEFAULT NULL, created_by VARCHAR(255) DEFAULT NULL, updated_by VARCHAR(255) DEFAULT NULL, INDEX IDX_EC3D7E073FC453F8 (docker_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE plugin (id SMALLINT UNSIGNED AUTO_INCREMENT NOT NULL, status_id SMALLINT UNSIGNED NOT NULL, label VARCHAR(128) NOT NULL, description LONGTEXT DEFAULT NULL, reference VARCHAR(64) DEFAULT NULL, version VARCHAR(8) DEFAULT NULL, aggregate_id VARCHAR(255) NOT NULL, created_by VARCHAR(255) DEFAULT NULL, updated_by VARCHAR(255) DEFAULT NULL, created_at DATETIME NOT NULL, updated_at DATETIME DEFAULT NULL, UNIQUE INDEX UNIQ_E96E2794AEA34913 (reference), UNIQUE INDEX UNIQ_E96E2794D0BBCCBE (aggregate_id), INDEX IDX_E96E27946BF700BD (status_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE plugin_status (id SMALLINT UNSIGNED AUTO_INCREMENT NOT NULL, label VARCHAR(128) NOT NULL, reference VARCHAR(64) DEFAULT NULL, created_by VARCHAR(255) DEFAULT NULL, updated_by VARCHAR(255) DEFAULT NULL, created_at DATETIME NOT NULL, updated_at DATETIME DEFAULT NULL, UNIQUE INDEX UNIQ_E6822A0AEA34913 (reference), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE tag (id INT AUTO_INCREMENT NOT NULL, label VARCHAR(255) NOT NULL, datas JSON NOT NULL COMMENT \'(DC2Type:json)\', created_at DATETIME NOT NULL, updated_at DATETIME DEFAULT NULL, created_by VARCHAR(255) DEFAULT NULL, updated_by VARCHAR(255) DEFAULT NULL, PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE `user` (id SMALLINT UNSIGNED AUTO_INCREMENT NOT NULL, status_id SMALLINT UNSIGNED DEFAULT NULL, type_id SMALLINT UNSIGNED DEFAULT NULL, first_name VARCHAR(128) NOT NULL, last_name VARCHAR(128) NOT NULL, email VARCHAR(255) NOT NULL, roles JSON NOT NULL COMMENT \'(DC2Type:json)\', password VARCHAR(255) NOT NULL, aggregate_id VARCHAR(255) NOT NULL, created_at DATETIME NOT NULL, updated_at DATETIME DEFAULT NULL, created_by VARCHAR(255) DEFAULT NULL, updated_by VARCHAR(255) DEFAULT NULL, UNIQUE INDEX UNIQ_8D93D649E7927C74 (email), UNIQUE INDEX UNIQ_8D93D649D0BBCCBE (aggregate_id), INDEX IDX_8D93D6496BF700BD (status_id), INDEX IDX_8D93D649C54C8C93 (type_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE user_status (id SMALLINT UNSIGNED AUTO_INCREMENT NOT NULL, label VARCHAR(128) NOT NULL, reference VARCHAR(64) DEFAULT NULL, created_at DATETIME NOT NULL, updated_at DATETIME DEFAULT NULL, created_by VARCHAR(255) DEFAULT NULL, updated_by VARCHAR(255) DEFAULT NULL, UNIQUE INDEX UNIQ_1E527E21AEA34913 (reference), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE user_type (id SMALLINT UNSIGNED AUTO_INCREMENT NOT NULL, label VARCHAR(128) NOT NULL, reference VARCHAR(64) DEFAULT NULL, created_at DATETIME NOT NULL, updated_at DATETIME DEFAULT NULL, created_by VARCHAR(255) DEFAULT NULL, updated_by VARCHAR(255) DEFAULT NULL, UNIQUE INDEX UNIQ_F65F1BE0AEA34913 (reference), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE zone (id INT UNSIGNED AUTO_INCREMENT NOT NULL, parent_zone_id INT UNSIGNED DEFAULT NULL, label VARCHAR(255) NOT NULL, area DOUBLE PRECISION UNSIGNED NOT NULL, created_by VARCHAR(255) DEFAULT NULL, updated_by VARCHAR(255) DEFAULT NULL, created_at DATETIME NOT NULL, updated_at DATETIME DEFAULT NULL, INDEX IDX_A0EBC007ED644BED (parent_zone_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE messenger_messages (id BIGINT AUTO_INCREMENT NOT NULL, body LONGTEXT NOT NULL, headers LONGTEXT NOT NULL, queue_name VARCHAR(190) NOT NULL, created_at DATETIME NOT NULL COMMENT \'(DC2Type:datetime_immutable)\', available_at DATETIME NOT NULL COMMENT \'(DC2Type:datetime_immutable)\', delivered_at DATETIME DEFAULT NULL COMMENT \'(DC2Type:datetime_immutable)\', INDEX IDX_75EA56E0FB7336F0 (queue_name), INDEX IDX_75EA56E0E3BD61CE (available_at), INDEX IDX_75EA56E016BA31DB (delivered_at), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('ALTER TABLE docker_custom_command ADD CONSTRAINT FK_EC3D7E073FC453F8 FOREIGN KEY (docker_id) REFERENCES docker (id)');
        $this->addSql('ALTER TABLE plugin ADD CONSTRAINT FK_E96E27946BF700BD FOREIGN KEY (status_id) REFERENCES plugin_status (id)');
        $this->addSql('ALTER TABLE `user` ADD CONSTRAINT FK_8D93D6496BF700BD FOREIGN KEY (status_id) REFERENCES user_status (id)');
        $this->addSql('ALTER TABLE `user` ADD CONSTRAINT FK_8D93D649C54C8C93 FOREIGN KEY (type_id) REFERENCES user_type (id)');
        $this->addSql('ALTER TABLE zone ADD CONSTRAINT FK_A0EBC007ED644BED FOREIGN KEY (parent_zone_id) REFERENCES zone (id)');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE docker_custom_command DROP FOREIGN KEY FK_EC3D7E073FC453F8');
        $this->addSql('ALTER TABLE plugin DROP FOREIGN KEY FK_E96E27946BF700BD');
        $this->addSql('ALTER TABLE `user` DROP FOREIGN KEY FK_8D93D6496BF700BD');
        $this->addSql('ALTER TABLE `user` DROP FOREIGN KEY FK_8D93D649C54C8C93');
        $this->addSql('ALTER TABLE zone DROP FOREIGN KEY FK_A0EBC007ED644BED');
        $this->addSql('DROP TABLE capability');
        $this->addSql('DROP TABLE capability_action');
        $this->addSql('DROP TABLE docker');
        $this->addSql('DROP TABLE docker_custom_command');
        $this->addSql('DROP TABLE plugin');
        $this->addSql('DROP TABLE plugin_status');
        $this->addSql('DROP TABLE tag');
        $this->addSql('DROP TABLE `user`');
        $this->addSql('DROP TABLE user_status');
        $this->addSql('DROP TABLE user_type');
        $this->addSql('DROP TABLE zone');
        $this->addSql('DROP TABLE messenger_messages');
    }
}

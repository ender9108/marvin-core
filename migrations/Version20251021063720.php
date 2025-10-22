<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20251021063720 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE location_zone ADD current_temperature DOUBLE PRECISION NOT NULL');
        $this->addSql('ALTER TABLE location_zone ADD current_power_consumption DOUBLE PRECISION NOT NULL');
        $this->addSql('ALTER TABLE location_zone ADD is_occupied BOOLEAN DEFAULT NULL');
        $this->addSql('ALTER TABLE location_zone ADD consecutive_no_motion_count INT DEFAULT NULL');
        $this->addSql('ALTER TABLE location_zone ADD last_metrics_update TIMESTAMP(0) WITHOUT TIME ZONE DEFAULT NULL');
        $this->addSql('ALTER TABLE location_zone ADD path_value VARCHAR(255) DEFAULT NULL');
        $this->addSql('ALTER TABLE location_zone ADD orientation_value VARCHAR(32) DEFAULT NULL');
        $this->addSql('ALTER TABLE location_zone DROP orientation');
        $this->addSql('ALTER TABLE security_user ALTER email TYPE VARCHAR(500)');
        $this->addSql('ALTER TABLE security_user ALTER email TYPE VARCHAR(500)');
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
        $this->addSql('ALTER TABLE location_zone ADD orientation VARCHAR(32) NOT NULL');
        $this->addSql('ALTER TABLE location_zone DROP current_temperature');
        $this->addSql('ALTER TABLE location_zone DROP current_power_consumption');
        $this->addSql('ALTER TABLE location_zone DROP is_occupied');
        $this->addSql('ALTER TABLE location_zone DROP consecutive_no_motion_count');
        $this->addSql('ALTER TABLE location_zone DROP last_metrics_update');
        $this->addSql('ALTER TABLE location_zone DROP path_value');
        $this->addSql('ALTER TABLE location_zone DROP orientation_value');
        $this->addSql('ALTER TABLE security_user ALTER email TYPE VARCHAR(500)');
    }
}

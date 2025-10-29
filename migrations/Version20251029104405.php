<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20251029104405 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE location_zone ADD no_motion_counter INT NOT NULL');
        $this->addSql('ALTER TABLE location_zone ADD active_sensors_count INT NOT NULL');
        $this->addSql('ALTER TABLE location_zone ADD device_ids TEXT NOT NULL');
        $this->addSql('ALTER TABLE location_zone ADD device_temperatures JSON NOT NULL');
        $this->addSql('ALTER TABLE location_zone ADD device_humidities JSON NOT NULL');
        $this->addSql('ALTER TABLE location_zone ADD device_power_consumptions JSON NOT NULL');
        $this->addSql('ALTER TABLE location_zone ADD zone_name_value VARCHAR(100) NOT NULL');
        $this->addSql('ALTER TABLE location_zone ADD current_temperature_value NUMERIC(5, 2) DEFAULT NULL');
        $this->addSql('ALTER TABLE location_zone ADD current_power_consumption_value DOUBLE PRECISION DEFAULT NULL');
        $this->addSql('ALTER TABLE location_zone ADD current_humidity_value DOUBLE PRECISION DEFAULT NULL');
        $this->addSql('ALTER TABLE location_zone ADD target_humidity_value DOUBLE PRECISION DEFAULT NULL');
        $this->addSql('ALTER TABLE location_zone DROP current_temperature');
        $this->addSql('ALTER TABLE location_zone DROP current_power_consumption');
        $this->addSql('ALTER TABLE location_zone DROP consecutive_no_motion_count');
        $this->addSql('ALTER TABLE location_zone DROP type');
        $this->addSql('ALTER TABLE location_zone DROP label_value');
        $this->addSql('ALTER TABLE location_zone ALTER icon TYPE VARCHAR(50)');
        $this->addSql('ALTER TABLE location_zone ALTER target_temperature_value TYPE NUMERIC(5, 2)');
        $this->addSql('COMMENT ON COLUMN location_zone.device_ids IS \'(DC2Type:simple_array)\'');
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
        $this->addSql('ALTER TABLE location_zone ADD current_temperature DOUBLE PRECISION DEFAULT NULL');
        $this->addSql('ALTER TABLE location_zone ADD current_power_consumption DOUBLE PRECISION DEFAULT NULL');
        $this->addSql('ALTER TABLE location_zone ADD consecutive_no_motion_count INT DEFAULT NULL');
        $this->addSql('ALTER TABLE location_zone ADD type VARCHAR(32) NOT NULL');
        $this->addSql('ALTER TABLE location_zone ADD label_value VARCHAR(255) NOT NULL');
        $this->addSql('ALTER TABLE location_zone DROP no_motion_counter');
        $this->addSql('ALTER TABLE location_zone DROP active_sensors_count');
        $this->addSql('ALTER TABLE location_zone DROP device_ids');
        $this->addSql('ALTER TABLE location_zone DROP device_temperatures');
        $this->addSql('ALTER TABLE location_zone DROP device_humidities');
        $this->addSql('ALTER TABLE location_zone DROP device_power_consumptions');
        $this->addSql('ALTER TABLE location_zone DROP zone_name_value');
        $this->addSql('ALTER TABLE location_zone DROP current_temperature_value');
        $this->addSql('ALTER TABLE location_zone DROP current_power_consumption_value');
        $this->addSql('ALTER TABLE location_zone DROP current_humidity_value');
        $this->addSql('ALTER TABLE location_zone DROP target_humidity_value');
        $this->addSql('ALTER TABLE location_zone ALTER icon TYPE VARCHAR(32)');
        $this->addSql('ALTER TABLE location_zone ALTER target_temperature_value TYPE DOUBLE PRECISION');
        $this->addSql('ALTER TABLE security_user ALTER email TYPE VARCHAR(500)');
    }
}

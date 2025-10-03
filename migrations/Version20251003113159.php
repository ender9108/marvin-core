<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20251003113159 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('CREATE TABLE domotic_device (id UUID NOT NULL, protocol_id UUID DEFAULT NULL, zone_id UUID DEFAULT NULL, technicalname VARCHAR(255) DEFAULT NULL, label_value VARCHAR(255) NOT NULL, updated_at_value TIMESTAMP(0) WITHOUT TIME ZONE DEFAULT NULL, created_at_value TIMESTAMP(0) WITHOUT TIME ZONE NOT NULL, PRIMARY KEY(id))');
        $this->addSql('CREATE INDEX IDX_24EE42B0CCD59258 ON domotic_device (protocol_id)');
        $this->addSql('CREATE INDEX IDX_24EE42B09F2C3FAB ON domotic_device (zone_id)');
        $this->addSql('COMMENT ON COLUMN domotic_device.id IS \'(DC2Type:device_id)\'');
        $this->addSql('COMMENT ON COLUMN domotic_device.protocol_id IS \'(DC2Type:protocol_id)\'');
        $this->addSql('COMMENT ON COLUMN domotic_device.zone_id IS \'(DC2Type:zone_id)\'');
        $this->addSql('CREATE TABLE domotic_groups_devices (device_id UUID NOT NULL, group_id UUID NOT NULL, PRIMARY KEY(device_id, group_id))');
        $this->addSql('CREATE INDEX IDX_EB29EB0E94A4C7D4 ON domotic_groups_devices (device_id)');
        $this->addSql('CREATE INDEX IDX_EB29EB0EFE54D947 ON domotic_groups_devices (group_id)');
        $this->addSql('COMMENT ON COLUMN domotic_groups_devices.device_id IS \'(DC2Type:device_id)\'');
        $this->addSql('COMMENT ON COLUMN domotic_groups_devices.group_id IS \'(DC2Type:group_id)\'');
        $this->addSql('CREATE TABLE domotic_group (id UUID NOT NULL, label_value VARCHAR(255) NOT NULL, slug_value VARCHAR(255) NOT NULL, updated_at_value TIMESTAMP(0) WITHOUT TIME ZONE DEFAULT NULL, created_at_value TIMESTAMP(0) WITHOUT TIME ZONE NOT NULL, PRIMARY KEY(id))');
        $this->addSql('COMMENT ON COLUMN domotic_group.id IS \'(DC2Type:group_id)\'');
        $this->addSql('ALTER TABLE domotic_device ADD CONSTRAINT FK_24EE42B0CCD59258 FOREIGN KEY (protocol_id) REFERENCES domotic_protocol (id) NOT DEFERRABLE INITIALLY IMMEDIATE');
        $this->addSql('ALTER TABLE domotic_device ADD CONSTRAINT FK_24EE42B09F2C3FAB FOREIGN KEY (zone_id) REFERENCES domotic_zone (id) NOT DEFERRABLE INITIALLY IMMEDIATE');
        $this->addSql('ALTER TABLE domotic_groups_devices ADD CONSTRAINT FK_EB29EB0E94A4C7D4 FOREIGN KEY (device_id) REFERENCES domotic_device (id) ON DELETE CASCADE NOT DEFERRABLE INITIALLY IMMEDIATE');
        $this->addSql('ALTER TABLE domotic_groups_devices ADD CONSTRAINT FK_EB29EB0EFE54D947 FOREIGN KEY (group_id) REFERENCES domotic_group (id) ON DELETE CASCADE NOT DEFERRABLE INITIALLY IMMEDIATE');
        $this->addSql('ALTER TABLE security_user ALTER email TYPE VARCHAR(500)');
        $this->addSql('ALTER TABLE security_user ALTER email TYPE VARCHAR(500)');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('CREATE SCHEMA public');
        $this->addSql('ALTER TABLE domotic_device DROP CONSTRAINT FK_24EE42B0CCD59258');
        $this->addSql('ALTER TABLE domotic_device DROP CONSTRAINT FK_24EE42B09F2C3FAB');
        $this->addSql('ALTER TABLE domotic_groups_devices DROP CONSTRAINT FK_EB29EB0E94A4C7D4');
        $this->addSql('ALTER TABLE domotic_groups_devices DROP CONSTRAINT FK_EB29EB0EFE54D947');
        $this->addSql('DROP TABLE domotic_device');
        $this->addSql('DROP TABLE domotic_groups_devices');
        $this->addSql('DROP TABLE domotic_group');
        $this->addSql('ALTER TABLE security_user ALTER email TYPE VARCHAR(500)');
    }
}

<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20251015150054 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('CREATE TABLE domotic_capability (id UUID NOT NULL, label_value VARCHAR(255) NOT NULL, reference_value VARCHAR(64) NOT NULL, PRIMARY KEY(id))');
        $this->addSql('CREATE UNIQUE INDEX UNIQ_A3B60CD4CBD63824 ON domotic_capability (reference_value)');
        $this->addSql('COMMENT ON COLUMN domotic_capability.id IS \'(DC2Type:capability_id)\'');
        $this->addSql('CREATE TABLE domotic_capability_action (id UUID NOT NULL, label_value VARCHAR(255) NOT NULL, reference_value VARCHAR(64) NOT NULL, PRIMARY KEY(id))');
        $this->addSql('CREATE UNIQUE INDEX UNIQ_56E8C763CBD63824 ON domotic_capability_action (reference_value)');
        $this->addSql('COMMENT ON COLUMN domotic_capability_action.id IS \'(DC2Type:capability_action_id)\'');
        $this->addSql('CREATE TABLE domotic_capability_composition (id UUID NOT NULL, capability_id UUID DEFAULT NULL, PRIMARY KEY(id))');
        $this->addSql('CREATE INDEX IDX_59A328BA92043242 ON domotic_capability_composition (capability_id)');
        $this->addSql('COMMENT ON COLUMN domotic_capability_composition.id IS \'(DC2Type:capability_composition_id)\'');
        $this->addSql('COMMENT ON COLUMN domotic_capability_composition.capability_id IS \'(DC2Type:capability_id)\'');
        $this->addSql('CREATE TABLE domotic_capability_state (id UUID NOT NULL, state_schema JSON NOT NULL, label_value VARCHAR(255) NOT NULL, reference_value VARCHAR(64) NOT NULL, PRIMARY KEY(id))');
        $this->addSql('CREATE UNIQUE INDEX UNIQ_86D37F20CBD63824 ON domotic_capability_state (reference_value)');
        $this->addSql('COMMENT ON COLUMN domotic_capability_state.id IS \'(DC2Type:capability_state_id)\'');
        $this->addSql('CREATE TABLE domotic_device (id UUID NOT NULL, protocol_id UUID DEFAULT NULL, zone_id UUID DEFAULT NULL, label_value VARCHAR(255) NOT NULL, technical_name_value VARCHAR(255) DEFAULT NULL, state_value JSON NOT NULL, updated_at_value TIMESTAMP(0) WITHOUT TIME ZONE DEFAULT NULL, created_at_value TIMESTAMP(0) WITHOUT TIME ZONE NOT NULL, PRIMARY KEY(id))');
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
        $this->addSql('CREATE TABLE device_capability_composition (device_id UUID NOT NULL, capability_composition_id UUID NOT NULL, PRIMARY KEY(device_id, capability_composition_id))');
        $this->addSql('CREATE INDEX IDX_90CA50CB94A4C7D4 ON device_capability_composition (device_id)');
        $this->addSql('CREATE INDEX IDX_90CA50CBF03406B ON device_capability_composition (capability_composition_id)');
        $this->addSql('COMMENT ON COLUMN device_capability_composition.device_id IS \'(DC2Type:device_id)\'');
        $this->addSql('COMMENT ON COLUMN device_capability_composition.capability_composition_id IS \'(DC2Type:capability_composition_id)\'');
        $this->addSql('CREATE TABLE domotic_device_action (id UUID NOT NULL, device_id UUID DEFAULT NULL, updated_at_value TIMESTAMP(0) WITHOUT TIME ZONE DEFAULT NULL, created_at_value TIMESTAMP(0) WITHOUT TIME ZONE NOT NULL, PRIMARY KEY(id))');
        $this->addSql('CREATE INDEX IDX_64AECE0194A4C7D4 ON domotic_device_action (device_id)');
        $this->addSql('COMMENT ON COLUMN domotic_device_action.id IS \'(DC2Type:device_action_id)\'');
        $this->addSql('COMMENT ON COLUMN domotic_device_action.device_id IS \'(DC2Type:device_id)\'');
        $this->addSql('CREATE TABLE domotic_group (id UUID NOT NULL, label_value VARCHAR(255) NOT NULL, slug_value VARCHAR(255) NOT NULL, updated_at_value TIMESTAMP(0) WITHOUT TIME ZONE DEFAULT NULL, created_at_value TIMESTAMP(0) WITHOUT TIME ZONE NOT NULL, PRIMARY KEY(id))');
        $this->addSql('COMMENT ON COLUMN domotic_group.id IS \'(DC2Type:group_id)\'');
        $this->addSql('CREATE TABLE domotic_protocol (id UUID NOT NULL, label_value VARCHAR(255) NOT NULL, reference_value VARCHAR(64) NOT NULL, status_value INT NOT NULL, description_value TEXT DEFAULT NULL, metadata_value JSON NOT NULL, updated_at_value TIMESTAMP(0) WITHOUT TIME ZONE DEFAULT NULL, created_at_value TIMESTAMP(0) WITHOUT TIME ZONE NOT NULL, PRIMARY KEY(id))');
        $this->addSql('CREATE UNIQUE INDEX UNIQ_33D5BCF5CBD63824 ON domotic_protocol (reference_value)');
        $this->addSql('COMMENT ON COLUMN domotic_protocol.id IS \'(DC2Type:protocol_id)\'');
        $this->addSql('CREATE TABLE domotic_zone (id UUID NOT NULL, parent_zone_id UUID DEFAULT NULL, label_value VARCHAR(255) NOT NULL, area_value DOUBLE PRECISION DEFAULT \'0.0\' NOT NULL, updated_at_value TIMESTAMP(0) WITHOUT TIME ZONE DEFAULT NULL, created_at_value TIMESTAMP(0) WITHOUT TIME ZONE NOT NULL, PRIMARY KEY(id))');
        $this->addSql('CREATE INDEX IDX_B7D69EFAED644BED ON domotic_zone (parent_zone_id)');
        $this->addSql('COMMENT ON COLUMN domotic_zone.id IS \'(DC2Type:zone_id)\'');
        $this->addSql('COMMENT ON COLUMN domotic_zone.parent_zone_id IS \'(DC2Type:zone_id)\'');
        $this->addSql('CREATE TABLE security_login_attempt (id UUID NOT NULL, user_id UUID NOT NULL, created_at_value TIMESTAMP(0) WITHOUT TIME ZONE NOT NULL, PRIMARY KEY(id))');
        $this->addSql('CREATE INDEX IDX_8128A205A76ED395 ON security_login_attempt (user_id)');
        $this->addSql('COMMENT ON COLUMN security_login_attempt.id IS \'(DC2Type:login_attempt_id)\'');
        $this->addSql('COMMENT ON COLUMN security_login_attempt.user_id IS \'(DC2Type:user_id)\'');
        $this->addSql('CREATE TABLE security_refresh_tokens (id SERIAL NOT NULL, refresh_token VARCHAR(128) NOT NULL, username VARCHAR(255) NOT NULL, valid TIMESTAMP(0) WITHOUT TIME ZONE NOT NULL, PRIMARY KEY(id))');
        $this->addSql('CREATE UNIQUE INDEX UNIQ_612A333CC74F2195 ON security_refresh_tokens (refresh_token)');
        $this->addSql('CREATE TABLE security_request_reset_password (id UUID NOT NULL, user_id UUID NOT NULL, token VARCHAR(255) NOT NULL, used BOOLEAN NOT NULL, expires_at_value TIMESTAMP(0) WITHOUT TIME ZONE NOT NULL, created_at_value TIMESTAMP(0) WITHOUT TIME ZONE NOT NULL, PRIMARY KEY(id))');
        $this->addSql('CREATE INDEX IDX_EA377A2AA76ED395 ON security_request_reset_password (user_id)');
        $this->addSql('COMMENT ON COLUMN security_request_reset_password.id IS \'(DC2Type:request_reset_password_id)\'');
        $this->addSql('COMMENT ON COLUMN security_request_reset_password.user_id IS \'(DC2Type:user_id)\'');
        $this->addSql('CREATE TABLE security_user (id UUID NOT NULL, email VARCHAR(500) NOT NULL, password VARCHAR(255) NOT NULL, firstname_value VARCHAR(255) NOT NULL, lastname_value VARCHAR(255) NOT NULL, roles_value JSON NOT NULL, locale_value VARCHAR(2) DEFAULT NULL, theme_value VARCHAR(32) DEFAULT NULL, status_value INT NOT NULL, type_value INT NOT NULL, timezone_value VARCHAR(255) NOT NULL, created_at_value TIMESTAMP(0) WITHOUT TIME ZONE NOT NULL, updated_at_value TIMESTAMP(0) WITHOUT TIME ZONE DEFAULT NULL, PRIMARY KEY(id))');
        $this->addSql('CREATE UNIQUE INDEX UNIQ_52825A88E7927C74 ON security_user (email)');
        $this->addSql('COMMENT ON COLUMN security_user.id IS \'(DC2Type:user_id)\'');
        $this->addSql('CREATE TABLE system_action_request (id UUID NOT NULL, correlation_id VARCHAR(255) NOT NULL, entity_type VARCHAR(255) NOT NULL, entity_id VARCHAR(255) NOT NULL, action VARCHAR(255) NOT NULL, status VARCHAR(32) NOT NULL, input JSON DEFAULT NULL, output VARCHAR(255) DEFAULT NULL, error VARCHAR(255) DEFAULT NULL, completed_at TIMESTAMP(0) WITHOUT TIME ZONE DEFAULT NULL, created_at TIMESTAMP(0) WITHOUT TIME ZONE NOT NULL, PRIMARY KEY(id))');
        $this->addSql('COMMENT ON COLUMN system_action_request.id IS \'(DC2Type:action_request_id)\'');
        $this->addSql('CREATE TABLE system_container (id UUID NOT NULL, image VARCHAR(255) NOT NULL, type VARCHAR(64) NOT NULL, status VARCHAR(128) DEFAULT NULL, ports JSON NOT NULL, volumes JSON NOT NULL, last_synced_at TIMESTAMP(0) WITHOUT TIME ZONE DEFAULT NULL, updated_at TIMESTAMP(0) WITHOUT TIME ZONE DEFAULT NULL, created_at TIMESTAMP(0) WITHOUT TIME ZONE NOT NULL, label_value VARCHAR(255) NOT NULL, docker_label_value VARCHAR(255) NOT NULL, allowed_actions_value JSON DEFAULT NULL, metadata_value JSON NOT NULL, PRIMARY KEY(id))');
        $this->addSql('COMMENT ON COLUMN system_container.id IS \'(DC2Type:container_id)\'');
        $this->addSql('CREATE TABLE system_worker (id UUID NOT NULL, command TEXT NOT NULL, type VARCHAR(255) NOT NULL, status VARCHAR(255) DEFAULT NULL, num_procs INT NOT NULL, priority INT NOT NULL, auto_start BOOLEAN NOT NULL, auto_restart BOOLEAN NOT NULL, last_synced_at TIMESTAMP(0) WITHOUT TIME ZONE DEFAULT NULL, created_at TIMESTAMP(0) WITHOUT TIME ZONE NOT NULL, updated_at TIMESTAMP(0) WITHOUT TIME ZONE DEFAULT NULL, label_value VARCHAR(255) NOT NULL, process_name_value VARCHAR(255) NOT NULL, allowed_actions_value JSON DEFAULT NULL, metadata_value JSON NOT NULL, PRIMARY KEY(id))');
        $this->addSql('COMMENT ON COLUMN system_worker.id IS \'(DC2Type:worker_id)\'');
        $this->addSql('CREATE TABLE messenger_messages (id BIGSERIAL NOT NULL, body TEXT NOT NULL, headers TEXT NOT NULL, queue_name VARCHAR(190) NOT NULL, created_at TIMESTAMP(0) WITHOUT TIME ZONE NOT NULL, available_at TIMESTAMP(0) WITHOUT TIME ZONE NOT NULL, delivered_at TIMESTAMP(0) WITHOUT TIME ZONE DEFAULT NULL, PRIMARY KEY(id))');
        $this->addSql('CREATE INDEX IDX_75EA56E0FB7336F0 ON messenger_messages (queue_name)');
        $this->addSql('CREATE INDEX IDX_75EA56E0E3BD61CE ON messenger_messages (available_at)');
        $this->addSql('CREATE INDEX IDX_75EA56E016BA31DB ON messenger_messages (delivered_at)');
        $this->addSql('COMMENT ON COLUMN messenger_messages.created_at IS \'(DC2Type:datetime_immutable)\'');
        $this->addSql('COMMENT ON COLUMN messenger_messages.available_at IS \'(DC2Type:datetime_immutable)\'');
        $this->addSql('COMMENT ON COLUMN messenger_messages.delivered_at IS \'(DC2Type:datetime_immutable)\'');
        $this->addSql('CREATE OR REPLACE FUNCTION notify_messenger_messages() RETURNS TRIGGER AS $$
            BEGIN
                PERFORM pg_notify(\'messenger_messages\', NEW.queue_name::text);
                RETURN NEW;
            END;
        $$ LANGUAGE plpgsql;');
        $this->addSql('DROP TRIGGER IF EXISTS notify_trigger ON messenger_messages;');
        $this->addSql('CREATE TRIGGER notify_trigger AFTER INSERT OR UPDATE ON messenger_messages FOR EACH ROW EXECUTE PROCEDURE notify_messenger_messages();');
        $this->addSql('ALTER TABLE domotic_capability_composition ADD CONSTRAINT FK_59A328BA92043242 FOREIGN KEY (capability_id) REFERENCES domotic_capability (id) NOT DEFERRABLE INITIALLY IMMEDIATE');
        $this->addSql('ALTER TABLE domotic_device ADD CONSTRAINT FK_24EE42B0CCD59258 FOREIGN KEY (protocol_id) REFERENCES domotic_protocol (id) NOT DEFERRABLE INITIALLY IMMEDIATE');
        $this->addSql('ALTER TABLE domotic_device ADD CONSTRAINT FK_24EE42B09F2C3FAB FOREIGN KEY (zone_id) REFERENCES domotic_zone (id) NOT DEFERRABLE INITIALLY IMMEDIATE');
        $this->addSql('ALTER TABLE domotic_groups_devices ADD CONSTRAINT FK_EB29EB0E94A4C7D4 FOREIGN KEY (device_id) REFERENCES domotic_device (id) ON DELETE CASCADE NOT DEFERRABLE INITIALLY IMMEDIATE');
        $this->addSql('ALTER TABLE domotic_groups_devices ADD CONSTRAINT FK_EB29EB0EFE54D947 FOREIGN KEY (group_id) REFERENCES domotic_group (id) ON DELETE CASCADE NOT DEFERRABLE INITIALLY IMMEDIATE');
        $this->addSql('ALTER TABLE device_capability_composition ADD CONSTRAINT FK_90CA50CB94A4C7D4 FOREIGN KEY (device_id) REFERENCES domotic_device (id) ON DELETE CASCADE NOT DEFERRABLE INITIALLY IMMEDIATE');
        $this->addSql('ALTER TABLE device_capability_composition ADD CONSTRAINT FK_90CA50CBF03406B FOREIGN KEY (capability_composition_id) REFERENCES domotic_capability_composition (id) ON DELETE CASCADE NOT DEFERRABLE INITIALLY IMMEDIATE');
        $this->addSql('ALTER TABLE domotic_device_action ADD CONSTRAINT FK_64AECE0194A4C7D4 FOREIGN KEY (device_id) REFERENCES domotic_device (id) NOT DEFERRABLE INITIALLY IMMEDIATE');
        $this->addSql('ALTER TABLE domotic_zone ADD CONSTRAINT FK_B7D69EFAED644BED FOREIGN KEY (parent_zone_id) REFERENCES domotic_zone (id) ON DELETE SET NULL NOT DEFERRABLE INITIALLY IMMEDIATE');
        $this->addSql('ALTER TABLE security_login_attempt ADD CONSTRAINT FK_8128A205A76ED395 FOREIGN KEY (user_id) REFERENCES security_user (id) ON DELETE CASCADE NOT DEFERRABLE INITIALLY IMMEDIATE');
        $this->addSql('ALTER TABLE security_request_reset_password ADD CONSTRAINT FK_EA377A2AA76ED395 FOREIGN KEY (user_id) REFERENCES security_user (id) ON DELETE CASCADE NOT DEFERRABLE INITIALLY IMMEDIATE');
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
        $this->addSql('ALTER TABLE domotic_capability_composition DROP CONSTRAINT FK_59A328BA92043242');
        $this->addSql('ALTER TABLE domotic_device DROP CONSTRAINT FK_24EE42B0CCD59258');
        $this->addSql('ALTER TABLE domotic_device DROP CONSTRAINT FK_24EE42B09F2C3FAB');
        $this->addSql('ALTER TABLE domotic_groups_devices DROP CONSTRAINT FK_EB29EB0E94A4C7D4');
        $this->addSql('ALTER TABLE domotic_groups_devices DROP CONSTRAINT FK_EB29EB0EFE54D947');
        $this->addSql('ALTER TABLE device_capability_composition DROP CONSTRAINT FK_90CA50CB94A4C7D4');
        $this->addSql('ALTER TABLE device_capability_composition DROP CONSTRAINT FK_90CA50CBF03406B');
        $this->addSql('ALTER TABLE domotic_device_action DROP CONSTRAINT FK_64AECE0194A4C7D4');
        $this->addSql('ALTER TABLE domotic_zone DROP CONSTRAINT FK_B7D69EFAED644BED');
        $this->addSql('ALTER TABLE security_login_attempt DROP CONSTRAINT FK_8128A205A76ED395');
        $this->addSql('ALTER TABLE security_request_reset_password DROP CONSTRAINT FK_EA377A2AA76ED395');
        $this->addSql('DROP TABLE domotic_capability');
        $this->addSql('DROP TABLE domotic_capability_action');
        $this->addSql('DROP TABLE domotic_capability_composition');
        $this->addSql('DROP TABLE domotic_capability_state');
        $this->addSql('DROP TABLE domotic_device');
        $this->addSql('DROP TABLE domotic_groups_devices');
        $this->addSql('DROP TABLE device_capability_composition');
        $this->addSql('DROP TABLE domotic_device_action');
        $this->addSql('DROP TABLE domotic_group');
        $this->addSql('DROP TABLE domotic_protocol');
        $this->addSql('DROP TABLE domotic_zone');
        $this->addSql('DROP TABLE security_login_attempt');
        $this->addSql('DROP TABLE security_refresh_tokens');
        $this->addSql('DROP TABLE security_request_reset_password');
        $this->addSql('DROP TABLE security_user');
        $this->addSql('DROP TABLE system_action_request');
        $this->addSql('DROP TABLE system_container');
        $this->addSql('DROP TABLE system_worker');
        $this->addSql('DROP TABLE messenger_messages');
    }
}

<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20251108082134 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql(<<<'SQL'
            CREATE TABLE device_device (
              id UUID NOT NULL,
              device_type VARCHAR(255) NOT NULL,
              status VARCHAR(255) NOT NULL,
              protocol VARCHAR(255) DEFAULT NULL,
              protocol_id UUID DEFAULT NULL,
              composite_type VARCHAR(255) DEFAULT NULL,
              composite_strategy VARCHAR(255) DEFAULT NULL,
              execution_strategy VARCHAR(255) DEFAULT NULL,
              child_device_ids JSON DEFAULT NULL,
              native_sub_groups JSON DEFAULT NULL,
              virtual_type VARCHAR(255) DEFAULT NULL,
              zone_id UUID DEFAULT NULL,
              created_at TIMESTAMP(0) WITHOUT TIME ZONE NOT NULL,
              last_seen_at TIMESTAMP(0) WITHOUT TIME ZONE DEFAULT NULL,
              last_state_update_at TIMESTAMP(0) WITHOUT TIME ZONE DEFAULT NULL,
              label_value VARCHAR(255) NOT NULL,
              description_value TEXT DEFAULT NULL,
              metadata_value JSON DEFAULT NULL,
              physical_address_value VARCHAR(100) NOT NULL,
              technical_name_value VARCHAR(255) NOT NULL,
              native_group_info_native_group_id VARCHAR(100) DEFAULT NULL,
              native_group_info_protocol_id VARCHAR(100) DEFAULT NULL,
              native_group_info_friendly_name VARCHAR(255) DEFAULT NULL,
              native_group_info_metadata JSON DEFAULT NULL,
              native_scene_info_native_scene_id VARCHAR(100) DEFAULT NULL,
              native_scene_info_protocol_id VARCHAR(100) DEFAULT NULL,
              native_scene_info_friendly_name VARCHAR(255) DEFAULT NULL,
              native_scene_info_group_id VARCHAR(100) DEFAULT NULL,
              native_scene_info_metadata JSON DEFAULT NULL,
              scene_states_value JSON DEFAULT NULL,
              virtual_config_value JSON DEFAULT NULL,
              PRIMARY KEY(id)
            )
        SQL);
        $this->addSql('CREATE INDEX idx_device_type ON device_device (device_type)');
        $this->addSql('CREATE INDEX idx_device_status ON device_device (status)');
        $this->addSql('CREATE INDEX idx_device_protocol ON device_device (protocol)');
        $this->addSql('CREATE INDEX idx_device_protocol_id ON device_device (protocol_id)');
        $this->addSql('CREATE INDEX idx_device_zone_id ON device_device (zone_id)');
        $this->addSql('CREATE INDEX idx_device_composite_type ON device_device (composite_type)');
        $this->addSql('CREATE INDEX idx_device_created_at ON device_device (created_at)');
        $this->addSql('COMMENT ON COLUMN device_device.id IS \'(DC2Type:device_id)\'');
        $this->addSql('COMMENT ON COLUMN device_device.protocol_id IS \'(DC2Type:protocol_id)\'');
        $this->addSql('COMMENT ON COLUMN device_device.zone_id IS \'(DC2Type:zone_id)\'');
        $this->addSql(<<<'SQL'
            CREATE TABLE device_device_capability (
              id UUID NOT NULL,
              device_id UUID NOT NULL,
              capability VARCHAR(255) NOT NULL,
              state_name VARCHAR(128) NOT NULL,
              current_value JSON DEFAULT NULL,
              last_updated_at TIMESTAMP(0) WITHOUT TIME ZONE DEFAULT NULL,
              metadata_value JSON DEFAULT NULL,
              PRIMARY KEY(id)
            )
        SQL);
        $this->addSql('CREATE INDEX IDX_A18C3F5194A4C7D4 ON device_device_capability (device_id)');
        $this->addSql('COMMENT ON COLUMN device_device_capability.id IS \'(DC2Type:device_capability_id)\'');
        $this->addSql('COMMENT ON COLUMN device_device_capability.device_id IS \'(DC2Type:device_id)\'');
        $this->addSql(<<<'SQL'
            CREATE TABLE device_pending_action (
              id UUID NOT NULL,
              device_id UUID NOT NULL,
              correlation_id UUID DEFAULT NULL,
              status VARCHAR(255) NOT NULL,
              capability VARCHAR(100) NOT NULL,
              action VARCHAR(100) NOT NULL,
              parameters JSON NOT NULL,
              result JSON DEFAULT NULL,
              error_message TEXT DEFAULT NULL,
              created_at TIMESTAMP(0) WITHOUT TIME ZONE NOT NULL,
              completed_at TIMESTAMP(0) WITHOUT TIME ZONE DEFAULT NULL,
              timeout_seconds INT NOT NULL,
              PRIMARY KEY(id)
            )
        SQL);
        $this->addSql('CREATE INDEX idx_device_id ON device_pending_action (device_id)');
        $this->addSql('CREATE INDEX idx_correlation_id ON device_pending_action (correlation_id)');
        $this->addSql('CREATE INDEX idx_status ON device_pending_action (status)');
        $this->addSql('CREATE INDEX idx_created_at ON device_pending_action (created_at)');
        $this->addSql('COMMENT ON COLUMN device_pending_action.id IS \'(DC2Type:pending_action_id)\'');
        $this->addSql('COMMENT ON COLUMN device_pending_action.device_id IS \'(DC2Type:device_id)\'');
        $this->addSql('COMMENT ON COLUMN device_pending_action.correlation_id IS \'(DC2Type:correlation_id)\'');
        $this->addSql(<<<'SQL'
            CREATE TABLE location_zone (
              id UUID NOT NULL,
              parent_id UUID DEFAULT NULL,
              slug VARCHAR(255) NOT NULL,
              icon VARCHAR(50) DEFAULT NULL,
              type VARCHAR(16) NOT NULL,
              is_occupied BOOLEAN DEFAULT NULL,
              no_motion_counter INT NOT NULL,
              active_sensors_count INT NOT NULL,
              last_metrics_update TIMESTAMP(0) WITHOUT TIME ZONE DEFAULT NULL,
              device_ids TEXT DEFAULT NULL,
              device_temperatures JSON NOT NULL,
              device_humidities JSON NOT NULL,
              device_power_consumptions JSON NOT NULL,
              orientation VARCHAR(32) DEFAULT NULL,
              updated_at TIMESTAMP(0) WITHOUT TIME ZONE DEFAULT NULL,
              created_at TIMESTAMP(0) WITHOUT TIME ZONE NOT NULL,
              zone_name_value VARCHAR(100) NOT NULL,
              current_temperature_value NUMERIC(5, 2) DEFAULT NULL,
              current_power_consumption_value NUMERIC(10, 2) DEFAULT NULL,
              current_humidity_value NUMERIC(4, 2) DEFAULT NULL,
              target_temperature_value NUMERIC(5, 2) DEFAULT NULL,
              target_power_consumption_value NUMERIC(10, 2) DEFAULT NULL,
              target_humidity_value NUMERIC(4, 2) DEFAULT NULL,
              surface_area_value NUMERIC(8, 2) DEFAULT NULL,
              color_value VARCHAR(9) DEFAULT NULL,
              metadata_value JSON DEFAULT NULL,
              PRIMARY KEY(id)
            )
        SQL);
        $this->addSql('CREATE INDEX IDX_20EF7547727ACA70 ON location_zone (parent_id)');
        $this->addSql('COMMENT ON COLUMN location_zone.id IS \'(DC2Type:zone_id)\'');
        $this->addSql('COMMENT ON COLUMN location_zone.parent_id IS \'(DC2Type:zone_id)\'');
        $this->addSql('COMMENT ON COLUMN location_zone.device_ids IS \'(DC2Type:simple_array)\'');
        $this->addSql(<<<'SQL'
            CREATE TABLE protocol_protocol (
              id UUID NOT NULL,
              transport_type VARCHAR(255) NOT NULL,
              status VARCHAR(255) NOT NULL,
              preferred_execution_mode VARCHAR(255) NOT NULL,
              created_at TIMESTAMP(0) WITHOUT TIME ZONE NOT NULL,
              updated_at TIMESTAMP(0) WITHOUT TIME ZONE DEFAULT NULL,
              name_value VARCHAR(255) NOT NULL,
              configuration_value JSON NOT NULL,
              PRIMARY KEY(id)
            )
        SQL);
        $this->addSql('COMMENT ON COLUMN protocol_protocol.id IS \'(DC2Type:protocol_id)\'');
        $this->addSql('COMMENT ON COLUMN protocol_protocol.created_at IS \'(DC2Type:datetime_immutable)\'');
        $this->addSql('COMMENT ON COLUMN protocol_protocol.updated_at IS \'(DC2Type:datetime_immutable)\'');
        $this->addSql(<<<'SQL'
            CREATE TABLE secret_secret (
              id UUID NOT NULL,
              scope VARCHAR(20) NOT NULL,
              category VARCHAR(30) NOT NULL,
              last_rotated_at TIMESTAMP(0) WITHOUT TIME ZONE DEFAULT NULL,
              expires_at TIMESTAMP(0) WITHOUT TIME ZONE DEFAULT NULL,
              created_at TIMESTAMP(0) WITHOUT TIME ZONE NOT NULL,
              updated_at TIMESTAMP(0) WITHOUT TIME ZONE DEFAULT NULL,
              key_value VARCHAR(255) NOT NULL,
              value_value VARCHAR(255) NOT NULL,
              rotation_policy_management VARCHAR(255) NOT NULL,
              rotation_policy_rotation_interval_days INT NOT NULL,
              rotation_policy_auto_rotate BOOLEAN NOT NULL,
              rotation_policy_rotation_command VARCHAR(255) DEFAULT NULL,
              metadata_value JSON DEFAULT NULL,
              PRIMARY KEY(id)
            )
        SQL);
        $this->addSql('COMMENT ON COLUMN secret_secret.id IS \'(DC2Type:secret_id)\'');
        $this->addSql('COMMENT ON COLUMN secret_secret.last_rotated_at IS \'(DC2Type:datetime_immutable)\'');
        $this->addSql('COMMENT ON COLUMN secret_secret.expires_at IS \'(DC2Type:datetime_immutable)\'');
        $this->addSql('COMMENT ON COLUMN secret_secret.created_at IS \'(DC2Type:datetime_immutable)\'');
        $this->addSql('COMMENT ON COLUMN secret_secret.updated_at IS \'(DC2Type:datetime_immutable)\'');
        $this->addSql(<<<'SQL'
            CREATE TABLE security_login_attempt (
              id UUID NOT NULL,
              user_id UUID NOT NULL,
              created_at TIMESTAMP(0) WITHOUT TIME ZONE NOT NULL,
              PRIMARY KEY(id)
            )
        SQL);
        $this->addSql('CREATE INDEX IDX_8128A205A76ED395 ON security_login_attempt (user_id)');
        $this->addSql('COMMENT ON COLUMN security_login_attempt.id IS \'(DC2Type:login_attempt_id)\'');
        $this->addSql('COMMENT ON COLUMN security_login_attempt.user_id IS \'(DC2Type:user_id)\'');
        $this->addSql(<<<'SQL'
            CREATE TABLE security_refresh_tokens (
              id SERIAL NOT NULL,
              refresh_token VARCHAR(128) NOT NULL,
              username VARCHAR(255) NOT NULL,
              valid TIMESTAMP(0) WITHOUT TIME ZONE NOT NULL,
              PRIMARY KEY(id)
            )
        SQL);
        $this->addSql('CREATE UNIQUE INDEX UNIQ_612A333CC74F2195 ON security_refresh_tokens (refresh_token)');
        $this->addSql(<<<'SQL'
            CREATE TABLE security_request_reset_password (
              id UUID NOT NULL,
              user_id UUID NOT NULL,
              token VARCHAR(255) NOT NULL,
              used BOOLEAN NOT NULL,
              created_at TIMESTAMP(0) WITHOUT TIME ZONE NOT NULL,
              expires_at_value TIMESTAMP(0) WITHOUT TIME ZONE NOT NULL,
              PRIMARY KEY(id)
            )
        SQL);
        $this->addSql('CREATE INDEX IDX_EA377A2AA76ED395 ON security_request_reset_password (user_id)');
        $this->addSql('COMMENT ON COLUMN security_request_reset_password.id IS \'(DC2Type:request_reset_password_id)\'');
        $this->addSql('COMMENT ON COLUMN security_request_reset_password.user_id IS \'(DC2Type:user_id)\'');
        $this->addSql(<<<'SQL'
            CREATE TABLE security_user (
              id UUID NOT NULL,
              email VARCHAR(500) NOT NULL,
              password VARCHAR(255) NOT NULL,
              updated_at TIMESTAMP(0) WITHOUT TIME ZONE DEFAULT NULL,
              created_at TIMESTAMP(0) WITHOUT TIME ZONE NOT NULL,
              firstname_value VARCHAR(255) NOT NULL,
              lastname_value VARCHAR(255) NOT NULL,
              roles_value JSON NOT NULL,
              locale_value VARCHAR(2) DEFAULT NULL,
              theme_value VARCHAR(32) DEFAULT NULL,
              status_value INT NOT NULL,
              type_value INT NOT NULL,
              timezone_value VARCHAR(255) NOT NULL,
              PRIMARY KEY(id)
            )
        SQL);
        $this->addSql('CREATE UNIQUE INDEX UNIQ_52825A88E7927C74 ON security_user (email)');
        $this->addSql('COMMENT ON COLUMN security_user.id IS \'(DC2Type:user_id)\'');
        $this->addSql(<<<'SQL'
            CREATE TABLE system_action_request (
              id UUID NOT NULL,
              correlation_id VARCHAR(255) NOT NULL,
              entity_type VARCHAR(32) NOT NULL,
              entity_id VARCHAR(64) NOT NULL,
              action VARCHAR(32) NOT NULL,
              status VARCHAR(32) NOT NULL,
              input JSON DEFAULT NULL,
              output TEXT DEFAULT NULL,
              error TEXT DEFAULT NULL,
              completed_at TIMESTAMP(0) WITHOUT TIME ZONE DEFAULT NULL,
              created_at TIMESTAMP(0) WITHOUT TIME ZONE NOT NULL,
              PRIMARY KEY(id)
            )
        SQL);
        $this->addSql('COMMENT ON COLUMN system_action_request.id IS \'(DC2Type:action_request_id)\'');
        $this->addSql(<<<'SQL'
            CREATE TABLE system_container (
              id UUID NOT NULL,
              type VARCHAR(64) NOT NULL,
              status VARCHAR(128) DEFAULT NULL,
              container_id VARCHAR(128) DEFAULT NULL,
              container_label VARCHAR(128) DEFAULT NULL,
              ports JSON NOT NULL,
              volumes JSON NOT NULL,
              last_synced_at TIMESTAMP(0) WITHOUT TIME ZONE DEFAULT NULL,
              created_at TIMESTAMP(0) WITHOUT TIME ZONE NOT NULL,
              service_label_value VARCHAR(255) NOT NULL,
              allowed_actions_value JSON DEFAULT NULL,
              image_value VARCHAR(255) NOT NULL,
              metadata_value JSON DEFAULT NULL,
              PRIMARY KEY(id)
            )
        SQL);
        $this->addSql('CREATE UNIQUE INDEX UNIQ_C47DE4A6BC21F742 ON system_container (container_id)');
        $this->addSql('COMMENT ON COLUMN system_container.id IS \'(DC2Type:container_id)\'');
        $this->addSql(<<<'SQL'
            CREATE TABLE system_worker (
              id UUID NOT NULL,
              type VARCHAR(255) NOT NULL,
              status VARCHAR(255) DEFAULT NULL,
              command TEXT NOT NULL,
              num_procs INT DEFAULT NULL,
              uptime VARCHAR(64) DEFAULT NULL,
              last_synced_at TIMESTAMP(0) WITHOUT TIME ZONE DEFAULT NULL,
              created_at TIMESTAMP(0) WITHOUT TIME ZONE NOT NULL,
              label_value VARCHAR(255) NOT NULL,
              allowed_actions_value JSON DEFAULT NULL,
              metadata_value JSON DEFAULT NULL,
              PRIMARY KEY(id)
            )
        SQL);
        $this->addSql('COMMENT ON COLUMN system_worker.id IS \'(DC2Type:worker_id)\'');
        $this->addSql(<<<'SQL'
            CREATE TABLE messenger_domain_events (
              id BIGSERIAL NOT NULL,
              body TEXT NOT NULL,
              headers TEXT NOT NULL,
              queue_name VARCHAR(190) NOT NULL,
              created_at TIMESTAMP(0) WITHOUT TIME ZONE NOT NULL,
              available_at TIMESTAMP(0) WITHOUT TIME ZONE NOT NULL,
              delivered_at TIMESTAMP(0) WITHOUT TIME ZONE DEFAULT NULL,
              PRIMARY KEY(id)
            )
        SQL);
        $this->addSql('CREATE INDEX IDX_83A32E41FB7336F0 ON messenger_domain_events (queue_name)');
        $this->addSql('CREATE INDEX IDX_83A32E41E3BD61CE ON messenger_domain_events (available_at)');
        $this->addSql('CREATE INDEX IDX_83A32E4116BA31DB ON messenger_domain_events (delivered_at)');
        $this->addSql('COMMENT ON COLUMN messenger_domain_events.created_at IS \'(DC2Type:datetime_immutable)\'');
        $this->addSql('COMMENT ON COLUMN messenger_domain_events.available_at IS \'(DC2Type:datetime_immutable)\'');
        $this->addSql('COMMENT ON COLUMN messenger_domain_events.delivered_at IS \'(DC2Type:datetime_immutable)\'');
        $this->addSql(<<<'SQL'
            CREATE
            OR REPLACE FUNCTION notify_messenger_domain_events() RETURNS TRIGGER AS $$ BEGIN
              PERFORM pg_notify(
                'messenger_domain_events', NEW.queue_name :: text
              );

              RETURN NEW;
            END;

            $$ LANGUAGE plpgsql;
        SQL);
        $this->addSql('DROP TRIGGER IF EXISTS notify_trigger ON messenger_domain_events;');
        $this->addSql(<<<'SQL'
            CREATE TRIGGER notify_trigger AFTER INSERT
            OR
            UPDATE
              ON messenger_domain_events FOR EACH ROW EXECUTE PROCEDURE notify_messenger_domain_events();
        SQL);
        $this->addSql(<<<'SQL'
            CREATE TABLE messenger_domain_commands (
              id BIGSERIAL NOT NULL,
              body TEXT NOT NULL,
              headers TEXT NOT NULL,
              queue_name VARCHAR(190) NOT NULL,
              created_at TIMESTAMP(0) WITHOUT TIME ZONE NOT NULL,
              available_at TIMESTAMP(0) WITHOUT TIME ZONE NOT NULL,
              delivered_at TIMESTAMP(0) WITHOUT TIME ZONE DEFAULT NULL,
              PRIMARY KEY(id)
            )
        SQL);
        $this->addSql('CREATE INDEX IDX_5013D35BFB7336F0 ON messenger_domain_commands (queue_name)');
        $this->addSql('CREATE INDEX IDX_5013D35BE3BD61CE ON messenger_domain_commands (available_at)');
        $this->addSql('CREATE INDEX IDX_5013D35B16BA31DB ON messenger_domain_commands (delivered_at)');
        $this->addSql('COMMENT ON COLUMN messenger_domain_commands.created_at IS \'(DC2Type:datetime_immutable)\'');
        $this->addSql('COMMENT ON COLUMN messenger_domain_commands.available_at IS \'(DC2Type:datetime_immutable)\'');
        $this->addSql('COMMENT ON COLUMN messenger_domain_commands.delivered_at IS \'(DC2Type:datetime_immutable)\'');
        $this->addSql(<<<'SQL'
            CREATE
            OR REPLACE FUNCTION notify_messenger_domain_commands() RETURNS TRIGGER AS $$ BEGIN
              PERFORM pg_notify(
                'messenger_domain_commands', NEW.queue_name :: text
              );

              RETURN NEW;
            END;

            $$ LANGUAGE plpgsql;
        SQL);
        $this->addSql('DROP TRIGGER IF EXISTS notify_trigger ON messenger_domain_commands;');
        $this->addSql(<<<'SQL'
            CREATE TRIGGER notify_trigger AFTER INSERT
            OR
            UPDATE
              ON messenger_domain_commands FOR EACH ROW EXECUTE PROCEDURE notify_messenger_domain_commands();
        SQL);
        $this->addSql(<<<'SQL'
            CREATE TABLE messenger_marvin_to_manager (
              id BIGSERIAL NOT NULL,
              body TEXT NOT NULL,
              headers TEXT NOT NULL,
              queue_name VARCHAR(190) NOT NULL,
              created_at TIMESTAMP(0) WITHOUT TIME ZONE NOT NULL,
              available_at TIMESTAMP(0) WITHOUT TIME ZONE NOT NULL,
              delivered_at TIMESTAMP(0) WITHOUT TIME ZONE DEFAULT NULL,
              PRIMARY KEY(id)
            )
        SQL);
        $this->addSql('CREATE INDEX IDX_DD0747E8FB7336F0 ON messenger_marvin_to_manager (queue_name)');
        $this->addSql('CREATE INDEX IDX_DD0747E8E3BD61CE ON messenger_marvin_to_manager (available_at)');
        $this->addSql('CREATE INDEX IDX_DD0747E816BA31DB ON messenger_marvin_to_manager (delivered_at)');
        $this->addSql('COMMENT ON COLUMN messenger_marvin_to_manager.created_at IS \'(DC2Type:datetime_immutable)\'');
        $this->addSql('COMMENT ON COLUMN messenger_marvin_to_manager.available_at IS \'(DC2Type:datetime_immutable)\'');
        $this->addSql('COMMENT ON COLUMN messenger_marvin_to_manager.delivered_at IS \'(DC2Type:datetime_immutable)\'');
        $this->addSql(<<<'SQL'
            CREATE
            OR REPLACE FUNCTION notify_messenger_marvin_to_manager() RETURNS TRIGGER AS $$ BEGIN
              PERFORM pg_notify(
                'messenger_marvin_to_manager', NEW.queue_name :: text
              );

              RETURN NEW;
            END;

            $$ LANGUAGE plpgsql;
        SQL);
        $this->addSql('DROP TRIGGER IF EXISTS notify_trigger ON messenger_marvin_to_manager;');
        $this->addSql(<<<'SQL'
            CREATE TRIGGER notify_trigger AFTER INSERT
            OR
            UPDATE
              ON messenger_marvin_to_manager FOR EACH ROW EXECUTE PROCEDURE notify_messenger_marvin_to_manager();
        SQL);
        $this->addSql(<<<'SQL'
            CREATE TABLE messenger_manager_to_marvin (
              id BIGSERIAL NOT NULL,
              body TEXT NOT NULL,
              headers TEXT NOT NULL,
              queue_name VARCHAR(190) NOT NULL,
              created_at TIMESTAMP(0) WITHOUT TIME ZONE NOT NULL,
              available_at TIMESTAMP(0) WITHOUT TIME ZONE NOT NULL,
              delivered_at TIMESTAMP(0) WITHOUT TIME ZONE DEFAULT NULL,
              PRIMARY KEY(id)
            )
        SQL);
        $this->addSql('CREATE INDEX IDX_28E43BD3FB7336F0 ON messenger_manager_to_marvin (queue_name)');
        $this->addSql('CREATE INDEX IDX_28E43BD3E3BD61CE ON messenger_manager_to_marvin (available_at)');
        $this->addSql('CREATE INDEX IDX_28E43BD316BA31DB ON messenger_manager_to_marvin (delivered_at)');
        $this->addSql('COMMENT ON COLUMN messenger_manager_to_marvin.created_at IS \'(DC2Type:datetime_immutable)\'');
        $this->addSql('COMMENT ON COLUMN messenger_manager_to_marvin.available_at IS \'(DC2Type:datetime_immutable)\'');
        $this->addSql('COMMENT ON COLUMN messenger_manager_to_marvin.delivered_at IS \'(DC2Type:datetime_immutable)\'');
        $this->addSql(<<<'SQL'
            CREATE
            OR REPLACE FUNCTION notify_messenger_manager_to_marvin() RETURNS TRIGGER AS $$ BEGIN
              PERFORM pg_notify(
                'messenger_manager_to_marvin', NEW.queue_name :: text
              );

              RETURN NEW;
            END;

            $$ LANGUAGE plpgsql;
        SQL);
        $this->addSql('DROP TRIGGER IF EXISTS notify_trigger ON messenger_manager_to_marvin;');
        $this->addSql(<<<'SQL'
            CREATE TRIGGER notify_trigger AFTER INSERT
            OR
            UPDATE
              ON messenger_manager_to_marvin FOR EACH ROW EXECUTE PROCEDURE notify_messenger_manager_to_marvin();
        SQL);
        $this->addSql(<<<'SQL'
            CREATE TABLE messenger_domotic_events (
              id BIGSERIAL NOT NULL,
              body TEXT NOT NULL,
              headers TEXT NOT NULL,
              queue_name VARCHAR(190) NOT NULL,
              created_at TIMESTAMP(0) WITHOUT TIME ZONE NOT NULL,
              available_at TIMESTAMP(0) WITHOUT TIME ZONE NOT NULL,
              delivered_at TIMESTAMP(0) WITHOUT TIME ZONE DEFAULT NULL,
              PRIMARY KEY(id)
            )
        SQL);
        $this->addSql('CREATE INDEX IDX_B93479DFB7336F0 ON messenger_domotic_events (queue_name)');
        $this->addSql('CREATE INDEX IDX_B93479DE3BD61CE ON messenger_domotic_events (available_at)');
        $this->addSql('CREATE INDEX IDX_B93479D16BA31DB ON messenger_domotic_events (delivered_at)');
        $this->addSql('COMMENT ON COLUMN messenger_domotic_events.created_at IS \'(DC2Type:datetime_immutable)\'');
        $this->addSql('COMMENT ON COLUMN messenger_domotic_events.available_at IS \'(DC2Type:datetime_immutable)\'');
        $this->addSql('COMMENT ON COLUMN messenger_domotic_events.delivered_at IS \'(DC2Type:datetime_immutable)\'');
        $this->addSql(<<<'SQL'
            CREATE
            OR REPLACE FUNCTION notify_messenger_domotic_events() RETURNS TRIGGER AS $$ BEGIN
              PERFORM pg_notify(
                'messenger_domotic_events', NEW.queue_name :: text
              );

              RETURN NEW;
            END;

            $$ LANGUAGE plpgsql;
        SQL);
        $this->addSql('DROP TRIGGER IF EXISTS notify_trigger ON messenger_domotic_events;');
        $this->addSql(<<<'SQL'
            CREATE TRIGGER notify_trigger AFTER INSERT
            OR
            UPDATE
              ON messenger_domotic_events FOR EACH ROW EXECUTE PROCEDURE notify_messenger_domotic_events();
        SQL);
        $this->addSql(<<<'SQL'
            CREATE TABLE messenger_domotic_commands (
              id BIGSERIAL NOT NULL,
              body TEXT NOT NULL,
              headers TEXT NOT NULL,
              queue_name VARCHAR(190) NOT NULL,
              created_at TIMESTAMP(0) WITHOUT TIME ZONE NOT NULL,
              available_at TIMESTAMP(0) WITHOUT TIME ZONE NOT NULL,
              delivered_at TIMESTAMP(0) WITHOUT TIME ZONE DEFAULT NULL,
              PRIMARY KEY(id)
            )
        SQL);
        $this->addSql('CREATE INDEX IDX_49F00884FB7336F0 ON messenger_domotic_commands (queue_name)');
        $this->addSql('CREATE INDEX IDX_49F00884E3BD61CE ON messenger_domotic_commands (available_at)');
        $this->addSql('CREATE INDEX IDX_49F0088416BA31DB ON messenger_domotic_commands (delivered_at)');
        $this->addSql('COMMENT ON COLUMN messenger_domotic_commands.created_at IS \'(DC2Type:datetime_immutable)\'');
        $this->addSql('COMMENT ON COLUMN messenger_domotic_commands.available_at IS \'(DC2Type:datetime_immutable)\'');
        $this->addSql('COMMENT ON COLUMN messenger_domotic_commands.delivered_at IS \'(DC2Type:datetime_immutable)\'');
        $this->addSql(<<<'SQL'
            CREATE
            OR REPLACE FUNCTION notify_messenger_domotic_commands() RETURNS TRIGGER AS $$ BEGIN
              PERFORM pg_notify(
                'messenger_domotic_commands', NEW.queue_name :: text
              );

              RETURN NEW;
            END;

            $$ LANGUAGE plpgsql;
        SQL);
        $this->addSql('DROP TRIGGER IF EXISTS notify_trigger ON messenger_domotic_commands;');
        $this->addSql(<<<'SQL'
            CREATE TRIGGER notify_trigger AFTER INSERT
            OR
            UPDATE
              ON messenger_domotic_commands FOR EACH ROW EXECUTE PROCEDURE notify_messenger_domotic_commands();
        SQL);
        $this->addSql(<<<'SQL'
            CREATE TABLE messenger_failed (
              id BIGSERIAL NOT NULL,
              body TEXT NOT NULL,
              headers TEXT NOT NULL,
              queue_name VARCHAR(190) NOT NULL,
              created_at TIMESTAMP(0) WITHOUT TIME ZONE NOT NULL,
              available_at TIMESTAMP(0) WITHOUT TIME ZONE NOT NULL,
              delivered_at TIMESTAMP(0) WITHOUT TIME ZONE DEFAULT NULL,
              PRIMARY KEY(id)
            )
        SQL);
        $this->addSql('CREATE INDEX IDX_BF048994FB7336F0 ON messenger_failed (queue_name)');
        $this->addSql('CREATE INDEX IDX_BF048994E3BD61CE ON messenger_failed (available_at)');
        $this->addSql('CREATE INDEX IDX_BF04899416BA31DB ON messenger_failed (delivered_at)');
        $this->addSql('COMMENT ON COLUMN messenger_failed.created_at IS \'(DC2Type:datetime_immutable)\'');
        $this->addSql('COMMENT ON COLUMN messenger_failed.available_at IS \'(DC2Type:datetime_immutable)\'');
        $this->addSql('COMMENT ON COLUMN messenger_failed.delivered_at IS \'(DC2Type:datetime_immutable)\'');
        $this->addSql(<<<'SQL'
            CREATE
            OR REPLACE FUNCTION notify_messenger_failed() RETURNS TRIGGER AS $$ BEGIN
              PERFORM pg_notify(
                'messenger_failed', NEW.queue_name :: text
              );

              RETURN NEW;
            END;

            $$ LANGUAGE plpgsql;
        SQL);
        $this->addSql('DROP TRIGGER IF EXISTS notify_trigger ON messenger_failed;');
        $this->addSql(<<<'SQL'
            CREATE TRIGGER notify_trigger AFTER INSERT
            OR
            UPDATE
              ON messenger_failed FOR EACH ROW EXECUTE PROCEDURE notify_messenger_failed();
        SQL);
        $this->addSql(<<<'SQL'
            ALTER TABLE
              device_device_capability
            ADD
              CONSTRAINT FK_A18C3F5194A4C7D4 FOREIGN KEY (device_id) REFERENCES device_device (id) NOT DEFERRABLE INITIALLY IMMEDIATE
        SQL);
        $this->addSql(<<<'SQL'
            ALTER TABLE
              location_zone
            ADD
              CONSTRAINT FK_20EF7547727ACA70 FOREIGN KEY (parent_id) REFERENCES location_zone (id) NOT DEFERRABLE INITIALLY IMMEDIATE
        SQL);
        $this->addSql(<<<'SQL'
            ALTER TABLE
              security_login_attempt
            ADD
              CONSTRAINT FK_8128A205A76ED395 FOREIGN KEY (user_id) REFERENCES security_user (id) ON DELETE CASCADE NOT DEFERRABLE INITIALLY IMMEDIATE
        SQL);
        $this->addSql(<<<'SQL'
            ALTER TABLE
              security_request_reset_password
            ADD
              CONSTRAINT FK_EA377A2AA76ED395 FOREIGN KEY (user_id) REFERENCES security_user (id) ON DELETE CASCADE NOT DEFERRABLE INITIALLY IMMEDIATE
        SQL);
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
        $this->addSql('ALTER TABLE device_device_capability DROP CONSTRAINT FK_A18C3F5194A4C7D4');
        $this->addSql('ALTER TABLE location_zone DROP CONSTRAINT FK_20EF7547727ACA70');
        $this->addSql('ALTER TABLE security_login_attempt DROP CONSTRAINT FK_8128A205A76ED395');
        $this->addSql('ALTER TABLE security_request_reset_password DROP CONSTRAINT FK_EA377A2AA76ED395');
        $this->addSql('DROP TABLE device_device');
        $this->addSql('DROP TABLE device_device_capability');
        $this->addSql('DROP TABLE device_pending_action');
        $this->addSql('DROP TABLE location_zone');
        $this->addSql('DROP TABLE protocol_protocol');
        $this->addSql('DROP TABLE secret_secret');
        $this->addSql('DROP TABLE security_login_attempt');
        $this->addSql('DROP TABLE security_refresh_tokens');
        $this->addSql('DROP TABLE security_request_reset_password');
        $this->addSql('DROP TABLE security_user');
        $this->addSql('DROP TABLE system_action_request');
        $this->addSql('DROP TABLE system_container');
        $this->addSql('DROP TABLE system_worker');
        $this->addSql('DROP TABLE messenger_domain_events');
        $this->addSql('DROP TABLE messenger_domain_commands');
        $this->addSql('DROP TABLE messenger_marvin_to_manager');
        $this->addSql('DROP TABLE messenger_manager_to_marvin');
        $this->addSql('DROP TABLE messenger_domotic_events');
        $this->addSql('DROP TABLE messenger_domotic_commands');
        $this->addSql('DROP TABLE messenger_failed');
    }
}

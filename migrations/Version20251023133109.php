<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20251023133109 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('CREATE TABLE location_zone (id UUID NOT NULL, current_temperature DOUBLE PRECISION NOT NULL, current_power_consumption DOUBLE PRECISION NOT NULL, is_occupied BOOLEAN DEFAULT NULL, consecutive_no_motion_count INT DEFAULT NULL, type VARCHAR(32) NOT NULL, icon VARCHAR(32) NOT NULL, parent_zone_id VARCHAR(32) NOT NULL, last_metrics_update TIMESTAMP(0) WITHOUT TIME ZONE DEFAULT NULL, updated_at TIMESTAMP(0) WITHOUT TIME ZONE DEFAULT NULL, created_at TIMESTAMP(0) WITHOUT TIME ZONE NOT NULL, label_value VARCHAR(255) NOT NULL, path_value VARCHAR(255) DEFAULT NULL, target_temperature_value DOUBLE PRECISION DEFAULT NULL, target_power_consumption_value DOUBLE PRECISION DEFAULT NULL, surface_area_value DOUBLE PRECISION DEFAULT NULL, orientation_value VARCHAR(32) DEFAULT NULL, color_value VARCHAR(9) DEFAULT NULL, metadata_value JSON NOT NULL, PRIMARY KEY(id))');
        $this->addSql('COMMENT ON COLUMN location_zone.id IS \'(DC2Type:zone_id)\'');
        $this->addSql('CREATE TABLE security_login_attempt (id UUID NOT NULL, user_id UUID NOT NULL, created_at TIMESTAMP(0) WITHOUT TIME ZONE NOT NULL, PRIMARY KEY(id))');
        $this->addSql('CREATE INDEX IDX_8128A205A76ED395 ON security_login_attempt (user_id)');
        $this->addSql('COMMENT ON COLUMN security_login_attempt.id IS \'(DC2Type:login_attempt_id)\'');
        $this->addSql('COMMENT ON COLUMN security_login_attempt.user_id IS \'(DC2Type:user_id)\'');
        $this->addSql('CREATE TABLE security_refresh_tokens (id SERIAL NOT NULL, refresh_token VARCHAR(128) NOT NULL, username VARCHAR(255) NOT NULL, valid TIMESTAMP(0) WITHOUT TIME ZONE NOT NULL, PRIMARY KEY(id))');
        $this->addSql('CREATE UNIQUE INDEX UNIQ_612A333CC74F2195 ON security_refresh_tokens (refresh_token)');
        $this->addSql('CREATE TABLE security_request_reset_password (id UUID NOT NULL, user_id UUID NOT NULL, token VARCHAR(255) NOT NULL, used BOOLEAN NOT NULL, created_at TIMESTAMP(0) WITHOUT TIME ZONE NOT NULL, expires_at_value TIMESTAMP(0) WITHOUT TIME ZONE NOT NULL, PRIMARY KEY(id))');
        $this->addSql('CREATE INDEX IDX_EA377A2AA76ED395 ON security_request_reset_password (user_id)');
        $this->addSql('COMMENT ON COLUMN security_request_reset_password.id IS \'(DC2Type:request_reset_password_id)\'');
        $this->addSql('COMMENT ON COLUMN security_request_reset_password.user_id IS \'(DC2Type:user_id)\'');
        $this->addSql('CREATE TABLE security_user (id UUID NOT NULL, email VARCHAR(500) NOT NULL, password VARCHAR(255) NOT NULL, updated_at TIMESTAMP(0) WITHOUT TIME ZONE DEFAULT NULL, created_at TIMESTAMP(0) WITHOUT TIME ZONE NOT NULL, firstname_value VARCHAR(255) NOT NULL, lastname_value VARCHAR(255) NOT NULL, roles_value JSON NOT NULL, locale_value VARCHAR(2) DEFAULT NULL, theme_value VARCHAR(32) DEFAULT NULL, status_value INT NOT NULL, type_value INT NOT NULL, timezone_value VARCHAR(255) NOT NULL, PRIMARY KEY(id))');
        $this->addSql('CREATE UNIQUE INDEX UNIQ_52825A88E7927C74 ON security_user (email)');
        $this->addSql('COMMENT ON COLUMN security_user.id IS \'(DC2Type:user_id)\'');
        $this->addSql('CREATE TABLE system_action_request (id UUID NOT NULL, correlation_id VARCHAR(255) NOT NULL, entity_type VARCHAR(32) NOT NULL, entity_id VARCHAR(64) NOT NULL, action VARCHAR(32) NOT NULL, status VARCHAR(32) NOT NULL, input JSON DEFAULT NULL, output VARCHAR(255) DEFAULT NULL, error VARCHAR(255) DEFAULT NULL, completed_at TIMESTAMP(0) WITHOUT TIME ZONE DEFAULT NULL, created_at TIMESTAMP(0) WITHOUT TIME ZONE NOT NULL, PRIMARY KEY(id))');
        $this->addSql('COMMENT ON COLUMN system_action_request.id IS \'(DC2Type:action_request_id)\'');
        $this->addSql('CREATE TABLE system_container (id UUID NOT NULL, container_id VARCHAR(128) DEFAULT NULL, container_label VARCHAR(128) DEFAULT NULL, image VARCHAR(255) DEFAULT NULL, type VARCHAR(64) NOT NULL, status VARCHAR(128) DEFAULT NULL, ports JSON NOT NULL, volumes JSON NOT NULL, last_synced_at TIMESTAMP(0) WITHOUT TIME ZONE DEFAULT NULL, created_at TIMESTAMP(0) WITHOUT TIME ZONE NOT NULL, service_label_value VARCHAR(255) NOT NULL, allowed_actions_value JSON DEFAULT NULL, metadata_value JSON NOT NULL, PRIMARY KEY(id))');
        $this->addSql('CREATE UNIQUE INDEX UNIQ_C47DE4A6BC21F742 ON system_container (container_id)');
        $this->addSql('COMMENT ON COLUMN system_container.id IS \'(DC2Type:container_id)\'');
        $this->addSql('CREATE TABLE system_worker (id UUID NOT NULL, type VARCHAR(255) NOT NULL, status VARCHAR(255) DEFAULT NULL, command TEXT NOT NULL, num_procs INT DEFAULT NULL, uptime VARCHAR(64) DEFAULT NULL, last_synced_at TIMESTAMP(0) WITHOUT TIME ZONE DEFAULT NULL, created_at TIMESTAMP(0) WITHOUT TIME ZONE NOT NULL, label_value VARCHAR(255) NOT NULL, allowed_actions_value JSON DEFAULT NULL, metadata_value JSON NOT NULL, PRIMARY KEY(id))');
        $this->addSql('COMMENT ON COLUMN system_worker.id IS \'(DC2Type:worker_id)\'');
        $this->addSql('CREATE TABLE messenger_domotic_events (id BIGSERIAL NOT NULL, body TEXT NOT NULL, headers TEXT NOT NULL, queue_name VARCHAR(190) NOT NULL, created_at TIMESTAMP(0) WITHOUT TIME ZONE NOT NULL, available_at TIMESTAMP(0) WITHOUT TIME ZONE NOT NULL, delivered_at TIMESTAMP(0) WITHOUT TIME ZONE DEFAULT NULL, PRIMARY KEY(id))');
        $this->addSql('CREATE INDEX IDX_B93479DFB7336F0 ON messenger_domotic_events (queue_name)');
        $this->addSql('CREATE INDEX IDX_B93479DE3BD61CE ON messenger_domotic_events (available_at)');
        $this->addSql('CREATE INDEX IDX_B93479D16BA31DB ON messenger_domotic_events (delivered_at)');
        $this->addSql('COMMENT ON COLUMN messenger_domotic_events.created_at IS \'(DC2Type:datetime_immutable)\'');
        $this->addSql('COMMENT ON COLUMN messenger_domotic_events.available_at IS \'(DC2Type:datetime_immutable)\'');
        $this->addSql('COMMENT ON COLUMN messenger_domotic_events.delivered_at IS \'(DC2Type:datetime_immutable)\'');
        $this->addSql('CREATE OR REPLACE FUNCTION notify_messenger_domotic_events() RETURNS TRIGGER AS $$
            BEGIN
                PERFORM pg_notify(\'messenger_domotic_events\', NEW.queue_name::text);
                RETURN NEW;
            END;
        $$ LANGUAGE plpgsql;');
        $this->addSql('DROP TRIGGER IF EXISTS notify_trigger ON messenger_domotic_events;');
        $this->addSql('CREATE TRIGGER notify_trigger AFTER INSERT OR UPDATE ON messenger_domotic_events FOR EACH ROW EXECUTE PROCEDURE notify_messenger_domotic_events();');
        $this->addSql('CREATE TABLE messenger_domotic_commands (id BIGSERIAL NOT NULL, body TEXT NOT NULL, headers TEXT NOT NULL, queue_name VARCHAR(190) NOT NULL, created_at TIMESTAMP(0) WITHOUT TIME ZONE NOT NULL, available_at TIMESTAMP(0) WITHOUT TIME ZONE NOT NULL, delivered_at TIMESTAMP(0) WITHOUT TIME ZONE DEFAULT NULL, PRIMARY KEY(id))');
        $this->addSql('CREATE INDEX IDX_49F00884FB7336F0 ON messenger_domotic_commands (queue_name)');
        $this->addSql('CREATE INDEX IDX_49F00884E3BD61CE ON messenger_domotic_commands (available_at)');
        $this->addSql('CREATE INDEX IDX_49F0088416BA31DB ON messenger_domotic_commands (delivered_at)');
        $this->addSql('COMMENT ON COLUMN messenger_domotic_commands.created_at IS \'(DC2Type:datetime_immutable)\'');
        $this->addSql('COMMENT ON COLUMN messenger_domotic_commands.available_at IS \'(DC2Type:datetime_immutable)\'');
        $this->addSql('COMMENT ON COLUMN messenger_domotic_commands.delivered_at IS \'(DC2Type:datetime_immutable)\'');
        $this->addSql('CREATE OR REPLACE FUNCTION notify_messenger_domotic_commands() RETURNS TRIGGER AS $$
            BEGIN
                PERFORM pg_notify(\'messenger_domotic_commands\', NEW.queue_name::text);
                RETURN NEW;
            END;
        $$ LANGUAGE plpgsql;');
        $this->addSql('DROP TRIGGER IF EXISTS notify_trigger ON messenger_domotic_commands;');
        $this->addSql('CREATE TRIGGER notify_trigger AFTER INSERT OR UPDATE ON messenger_domotic_commands FOR EACH ROW EXECUTE PROCEDURE notify_messenger_domotic_commands();');
        $this->addSql('CREATE TABLE messenger_failed (id BIGSERIAL NOT NULL, body TEXT NOT NULL, headers TEXT NOT NULL, queue_name VARCHAR(190) NOT NULL, created_at TIMESTAMP(0) WITHOUT TIME ZONE NOT NULL, available_at TIMESTAMP(0) WITHOUT TIME ZONE NOT NULL, delivered_at TIMESTAMP(0) WITHOUT TIME ZONE DEFAULT NULL, PRIMARY KEY(id))');
        $this->addSql('CREATE INDEX IDX_BF048994FB7336F0 ON messenger_failed (queue_name)');
        $this->addSql('CREATE INDEX IDX_BF048994E3BD61CE ON messenger_failed (available_at)');
        $this->addSql('CREATE INDEX IDX_BF04899416BA31DB ON messenger_failed (delivered_at)');
        $this->addSql('COMMENT ON COLUMN messenger_failed.created_at IS \'(DC2Type:datetime_immutable)\'');
        $this->addSql('COMMENT ON COLUMN messenger_failed.available_at IS \'(DC2Type:datetime_immutable)\'');
        $this->addSql('COMMENT ON COLUMN messenger_failed.delivered_at IS \'(DC2Type:datetime_immutable)\'');
        $this->addSql('CREATE OR REPLACE FUNCTION notify_messenger_failed() RETURNS TRIGGER AS $$
            BEGIN
                PERFORM pg_notify(\'messenger_failed\', NEW.queue_name::text);
                RETURN NEW;
            END;
        $$ LANGUAGE plpgsql;');
        $this->addSql('DROP TRIGGER IF EXISTS notify_trigger ON messenger_failed;');
        $this->addSql('CREATE TRIGGER notify_trigger AFTER INSERT OR UPDATE ON messenger_failed FOR EACH ROW EXECUTE PROCEDURE notify_messenger_failed();');
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
        $this->addSql('ALTER TABLE security_login_attempt DROP CONSTRAINT FK_8128A205A76ED395');
        $this->addSql('ALTER TABLE security_request_reset_password DROP CONSTRAINT FK_EA377A2AA76ED395');
        $this->addSql('DROP TABLE location_zone');
        $this->addSql('DROP TABLE security_login_attempt');
        $this->addSql('DROP TABLE security_refresh_tokens');
        $this->addSql('DROP TABLE security_request_reset_password');
        $this->addSql('DROP TABLE security_user');
        $this->addSql('DROP TABLE system_action_request');
        $this->addSql('DROP TABLE system_container');
        $this->addSql('DROP TABLE system_worker');
        $this->addSql('DROP TABLE messenger_domotic_events');
        $this->addSql('DROP TABLE messenger_domotic_commands');
        $this->addSql('DROP TABLE messenger_failed');
    }
}

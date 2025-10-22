<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20251022065211 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('DROP SEQUENCE messenger_messages_id_seq CASCADE');
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
        $this->addSql('CREATE INDEX IDX_311C5386FB7336F0 ON messenger_failed (queue_name)');
        $this->addSql('CREATE INDEX IDX_311C5386E3BD61CE ON messenger_failed (available_at)');
        $this->addSql('CREATE INDEX IDX_311C538616BA31DB ON messenger_failed (delivered_at)');
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
        $this->addSql('DROP TABLE messenger_messages');
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
        $this->addSql('CREATE SEQUENCE messenger_messages_id_seq INCREMENT BY 1 MINVALUE 1 START 1');
        $this->addSql('CREATE TABLE messenger_messages (id BIGSERIAL NOT NULL, body TEXT NOT NULL, headers TEXT NOT NULL, queue_name VARCHAR(190) NOT NULL, created_at TIMESTAMP(0) WITHOUT TIME ZONE NOT NULL, available_at TIMESTAMP(0) WITHOUT TIME ZONE NOT NULL, delivered_at TIMESTAMP(0) WITHOUT TIME ZONE DEFAULT NULL, PRIMARY KEY(id))');
        $this->addSql('CREATE INDEX idx_75ea56e016ba31db ON messenger_messages (delivered_at)');
        $this->addSql('CREATE INDEX idx_75ea56e0e3bd61ce ON messenger_messages (available_at)');
        $this->addSql('CREATE INDEX idx_75ea56e0fb7336f0 ON messenger_messages (queue_name)');
        $this->addSql('COMMENT ON COLUMN messenger_messages.created_at IS \'(DC2Type:datetime_immutable)\'');
        $this->addSql('COMMENT ON COLUMN messenger_messages.available_at IS \'(DC2Type:datetime_immutable)\'');
        $this->addSql('COMMENT ON COLUMN messenger_messages.delivered_at IS \'(DC2Type:datetime_immutable)\'');
        $this->addSql('DROP TABLE messenger_domotic_events');
        $this->addSql('DROP TABLE messenger_domotic_commands');
        $this->addSql('DROP TABLE messenger_failed');
        $this->addSql('ALTER TABLE security_user ALTER email TYPE VARCHAR(500)');
    }
}

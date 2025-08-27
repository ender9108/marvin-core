<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20250826144153 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('CREATE TABLE capability (id UUID NOT NULL, label VARCHAR(255) NOT NULL, reference VARCHAR(128) NOT NULL, created_by VARCHAR(255) DEFAULT NULL, updated_by VARCHAR(255) DEFAULT NULL, created_at TIMESTAMP(0) WITHOUT TIME ZONE NOT NULL, updated_at TIMESTAMP(0) WITHOUT TIME ZONE DEFAULT NULL, PRIMARY KEY(id))');
        $this->addSql('COMMENT ON COLUMN capability.id IS \'(DC2Type:uuid)\'');
        $this->addSql('CREATE TABLE capability_action (id UUID NOT NULL, label VARCHAR(255) NOT NULL, reference VARCHAR(128) NOT NULL, created_by VARCHAR(255) DEFAULT NULL, updated_by VARCHAR(255) DEFAULT NULL, created_at TIMESTAMP(0) WITHOUT TIME ZONE NOT NULL, updated_at TIMESTAMP(0) WITHOUT TIME ZONE DEFAULT NULL, PRIMARY KEY(id))');
        $this->addSql('COMMENT ON COLUMN capability_action.id IS \'(DC2Type:uuid)\'');
        $this->addSql('CREATE TABLE capability_composition (id UUID NOT NULL, capability_id UUID NOT NULL, label VARCHAR(255) NOT NULL, reference VARCHAR(128) NOT NULL, created_by VARCHAR(255) DEFAULT NULL, updated_by VARCHAR(255) DEFAULT NULL, created_at TIMESTAMP(0) WITHOUT TIME ZONE NOT NULL, updated_at TIMESTAMP(0) WITHOUT TIME ZONE DEFAULT NULL, PRIMARY KEY(id))');
        $this->addSql('CREATE INDEX IDX_E109066892043242 ON capability_composition (capability_id)');
        $this->addSql('COMMENT ON COLUMN capability_composition.id IS \'(DC2Type:uuid)\'');
        $this->addSql('COMMENT ON COLUMN capability_composition.capability_id IS \'(DC2Type:uuid)\'');
        $this->addSql('CREATE TABLE capability_composition_capability_action (capability_composition_id UUID NOT NULL, capability_action_id UUID NOT NULL, PRIMARY KEY(capability_composition_id, capability_action_id))');
        $this->addSql('CREATE INDEX IDX_BAEECCCF03406B ON capability_composition_capability_action (capability_composition_id)');
        $this->addSql('CREATE INDEX IDX_BAEECCC3AD9EF42 ON capability_composition_capability_action (capability_action_id)');
        $this->addSql('COMMENT ON COLUMN capability_composition_capability_action.capability_composition_id IS \'(DC2Type:uuid)\'');
        $this->addSql('COMMENT ON COLUMN capability_composition_capability_action.capability_action_id IS \'(DC2Type:uuid)\'');
        $this->addSql('CREATE TABLE capability_composition_capability_state (capability_composition_id UUID NOT NULL, capability_state_id UUID NOT NULL, PRIMARY KEY(capability_composition_id, capability_state_id))');
        $this->addSql('CREATE INDEX IDX_A8E3D2DAF03406B ON capability_composition_capability_state (capability_composition_id)');
        $this->addSql('CREATE INDEX IDX_A8E3D2DA89E12A25 ON capability_composition_capability_state (capability_state_id)');
        $this->addSql('COMMENT ON COLUMN capability_composition_capability_state.capability_composition_id IS \'(DC2Type:uuid)\'');
        $this->addSql('COMMENT ON COLUMN capability_composition_capability_state.capability_state_id IS \'(DC2Type:uuid)\'');
        $this->addSql('CREATE TABLE capability_state (id UUID NOT NULL, label VARCHAR(255) NOT NULL, reference VARCHAR(128) NOT NULL, schema JSON NOT NULL, created_by VARCHAR(255) DEFAULT NULL, updated_by VARCHAR(255) DEFAULT NULL, created_at TIMESTAMP(0) WITHOUT TIME ZONE NOT NULL, updated_at TIMESTAMP(0) WITHOUT TIME ZONE DEFAULT NULL, PRIMARY KEY(id))');
        $this->addSql('COMMENT ON COLUMN capability_state.id IS \'(DC2Type:uuid)\'');
        $this->addSql('CREATE TABLE device (id UUID NOT NULL, name VARCHAR(255) NOT NULL, technical_name VARCHAR(255) NOT NULL, created_by VARCHAR(255) DEFAULT NULL, updated_by VARCHAR(255) DEFAULT NULL, created_at TIMESTAMP(0) WITHOUT TIME ZONE NOT NULL, updated_at TIMESTAMP(0) WITHOUT TIME ZONE DEFAULT NULL, PRIMARY KEY(id))');
        $this->addSql('COMMENT ON COLUMN device.id IS \'(DC2Type:uuid)\'');
        $this->addSql('CREATE TABLE device_capability_composition (device_id UUID NOT NULL, capability_composition_id UUID NOT NULL, PRIMARY KEY(device_id, capability_composition_id))');
        $this->addSql('CREATE INDEX IDX_90CA50CB94A4C7D4 ON device_capability_composition (device_id)');
        $this->addSql('CREATE INDEX IDX_90CA50CBF03406B ON device_capability_composition (capability_composition_id)');
        $this->addSql('COMMENT ON COLUMN device_capability_composition.device_id IS \'(DC2Type:uuid)\'');
        $this->addSql('COMMENT ON COLUMN device_capability_composition.capability_composition_id IS \'(DC2Type:uuid)\'');
        $this->addSql('CREATE TABLE docker (id SERIAL NOT NULL, container_id VARCHAR(255) NOT NULL, container_name VARCHAR(255) NOT NULL, container_image VARCHAR(255) DEFAULT NULL, container_service VARCHAR(128) DEFAULT NULL, container_state VARCHAR(128) DEFAULT NULL, container_status VARCHAR(255) DEFAULT NULL, container_project VARCHAR(255) DEFAULT NULL, definition JSON NOT NULL, created_at TIMESTAMP(0) WITHOUT TIME ZONE NOT NULL, updated_at TIMESTAMP(0) WITHOUT TIME ZONE DEFAULT NULL, created_by VARCHAR(255) DEFAULT NULL, updated_by VARCHAR(255) DEFAULT NULL, PRIMARY KEY(id))');
        $this->addSql('CREATE TABLE docker_custom_command (id SERIAL NOT NULL, docker_id INT NOT NULL, reference VARCHAR(64) NOT NULL, command TEXT NOT NULL, created_at TIMESTAMP(0) WITHOUT TIME ZONE NOT NULL, updated_at TIMESTAMP(0) WITHOUT TIME ZONE DEFAULT NULL, created_by VARCHAR(255) DEFAULT NULL, updated_by VARCHAR(255) DEFAULT NULL, PRIMARY KEY(id))');
        $this->addSql('CREATE INDEX IDX_EC3D7E073FC453F8 ON docker_custom_command (docker_id)');
        $this->addSql('CREATE TABLE "group" (id UUID NOT NULL, name VARCHAR(255) NOT NULL, slug VARCHAR(255) NOT NULL, created_by VARCHAR(255) DEFAULT NULL, updated_by VARCHAR(255) DEFAULT NULL, created_at TIMESTAMP(0) WITHOUT TIME ZONE NOT NULL, updated_at TIMESTAMP(0) WITHOUT TIME ZONE DEFAULT NULL, PRIMARY KEY(id))');
        $this->addSql('COMMENT ON COLUMN "group".id IS \'(DC2Type:uuid)\'');
        $this->addSql('CREATE TABLE group_device (group_id UUID NOT NULL, device_id UUID NOT NULL, PRIMARY KEY(group_id, device_id))');
        $this->addSql('CREATE INDEX IDX_DAA96E5EFE54D947 ON group_device (group_id)');
        $this->addSql('CREATE INDEX IDX_DAA96E5E94A4C7D4 ON group_device (device_id)');
        $this->addSql('COMMENT ON COLUMN group_device.group_id IS \'(DC2Type:uuid)\'');
        $this->addSql('COMMENT ON COLUMN group_device.device_id IS \'(DC2Type:uuid)\'');
        $this->addSql('CREATE TABLE plugin (id UUID NOT NULL, status_id UUID NOT NULL, label VARCHAR(128) NOT NULL, description TEXT DEFAULT NULL, reference VARCHAR(64) DEFAULT NULL, version VARCHAR(8) DEFAULT NULL, created_by VARCHAR(255) DEFAULT NULL, updated_by VARCHAR(255) DEFAULT NULL, created_at TIMESTAMP(0) WITHOUT TIME ZONE NOT NULL, updated_at TIMESTAMP(0) WITHOUT TIME ZONE DEFAULT NULL, PRIMARY KEY(id))');
        $this->addSql('CREATE UNIQUE INDEX UNIQ_E96E2794AEA34913 ON plugin (reference)');
        $this->addSql('CREATE INDEX IDX_E96E27946BF700BD ON plugin (status_id)');
        $this->addSql('COMMENT ON COLUMN plugin.id IS \'(DC2Type:uuid)\'');
        $this->addSql('COMMENT ON COLUMN plugin.status_id IS \'(DC2Type:uuid)\'');
        $this->addSql('CREATE TABLE plugin_status (id UUID NOT NULL, label VARCHAR(128) NOT NULL, reference VARCHAR(64) DEFAULT NULL, created_by VARCHAR(255) DEFAULT NULL, updated_by VARCHAR(255) DEFAULT NULL, created_at TIMESTAMP(0) WITHOUT TIME ZONE NOT NULL, updated_at TIMESTAMP(0) WITHOUT TIME ZONE DEFAULT NULL, PRIMARY KEY(id))');
        $this->addSql('CREATE UNIQUE INDEX UNIQ_E6822A0AEA34913 ON plugin_status (reference)');
        $this->addSql('COMMENT ON COLUMN plugin_status.id IS \'(DC2Type:uuid)\'');
        $this->addSql('CREATE TABLE protocol (id UUID NOT NULL, status_id UUID NOT NULL, label VARCHAR(128) NOT NULL, reference VARCHAR(64) NOT NULL, description TEXT DEFAULT NULL, created_by VARCHAR(255) DEFAULT NULL, updated_by VARCHAR(255) DEFAULT NULL, created_at TIMESTAMP(0) WITHOUT TIME ZONE NOT NULL, updated_at TIMESTAMP(0) WITHOUT TIME ZONE DEFAULT NULL, PRIMARY KEY(id))');
        $this->addSql('CREATE INDEX IDX_C8C0BC4C6BF700BD ON protocol (status_id)');
        $this->addSql('COMMENT ON COLUMN protocol.id IS \'(DC2Type:uuid)\'');
        $this->addSql('COMMENT ON COLUMN protocol.status_id IS \'(DC2Type:uuid)\'');
        $this->addSql('CREATE TABLE protocol_status (id UUID NOT NULL, label VARCHAR(128) NOT NULL, reference VARCHAR(64) NOT NULL, created_by VARCHAR(255) DEFAULT NULL, updated_by VARCHAR(255) DEFAULT NULL, created_at TIMESTAMP(0) WITHOUT TIME ZONE NOT NULL, updated_at TIMESTAMP(0) WITHOUT TIME ZONE DEFAULT NULL, PRIMARY KEY(id))');
        $this->addSql('COMMENT ON COLUMN protocol_status.id IS \'(DC2Type:uuid)\'');
        $this->addSql('CREATE TABLE "user" (id UUID NOT NULL, status_id UUID DEFAULT NULL, type_id UUID DEFAULT NULL, first_name VARCHAR(128) NOT NULL, last_name VARCHAR(128) NOT NULL, email VARCHAR(255) NOT NULL, roles JSON NOT NULL, password VARCHAR(255) NOT NULL, created_at TIMESTAMP(0) WITHOUT TIME ZONE NOT NULL, updated_at TIMESTAMP(0) WITHOUT TIME ZONE DEFAULT NULL, created_by VARCHAR(255) DEFAULT NULL, updated_by VARCHAR(255) DEFAULT NULL, PRIMARY KEY(id))');
        $this->addSql('CREATE UNIQUE INDEX UNIQ_8D93D649E7927C74 ON "user" (email)');
        $this->addSql('CREATE INDEX IDX_8D93D6496BF700BD ON "user" (status_id)');
        $this->addSql('CREATE INDEX IDX_8D93D649C54C8C93 ON "user" (type_id)');
        $this->addSql('COMMENT ON COLUMN "user".id IS \'(DC2Type:uuid)\'');
        $this->addSql('COMMENT ON COLUMN "user".status_id IS \'(DC2Type:uuid)\'');
        $this->addSql('COMMENT ON COLUMN "user".type_id IS \'(DC2Type:uuid)\'');
        $this->addSql('CREATE TABLE user_status (id UUID NOT NULL, label VARCHAR(128) NOT NULL, reference VARCHAR(64) DEFAULT NULL, created_at TIMESTAMP(0) WITHOUT TIME ZONE NOT NULL, updated_at TIMESTAMP(0) WITHOUT TIME ZONE DEFAULT NULL, created_by VARCHAR(255) DEFAULT NULL, updated_by VARCHAR(255) DEFAULT NULL, PRIMARY KEY(id))');
        $this->addSql('CREATE UNIQUE INDEX UNIQ_1E527E21AEA34913 ON user_status (reference)');
        $this->addSql('COMMENT ON COLUMN user_status.id IS \'(DC2Type:uuid)\'');
        $this->addSql('CREATE TABLE user_type (id UUID NOT NULL, label VARCHAR(128) NOT NULL, reference VARCHAR(64) DEFAULT NULL, created_at TIMESTAMP(0) WITHOUT TIME ZONE NOT NULL, updated_at TIMESTAMP(0) WITHOUT TIME ZONE DEFAULT NULL, created_by VARCHAR(255) DEFAULT NULL, updated_by VARCHAR(255) DEFAULT NULL, PRIMARY KEY(id))');
        $this->addSql('CREATE UNIQUE INDEX UNIQ_F65F1BE0AEA34913 ON user_type (reference)');
        $this->addSql('COMMENT ON COLUMN user_type.id IS \'(DC2Type:uuid)\'');
        $this->addSql('CREATE TABLE zone (id UUID NOT NULL, parent_zone_id UUID DEFAULT NULL, label VARCHAR(255) NOT NULL, area DOUBLE PRECISION NOT NULL, created_by VARCHAR(255) DEFAULT NULL, updated_by VARCHAR(255) DEFAULT NULL, created_at TIMESTAMP(0) WITHOUT TIME ZONE NOT NULL, updated_at TIMESTAMP(0) WITHOUT TIME ZONE DEFAULT NULL, PRIMARY KEY(id))');
        $this->addSql('CREATE INDEX IDX_A0EBC007ED644BED ON zone (parent_zone_id)');
        $this->addSql('COMMENT ON COLUMN zone.id IS \'(DC2Type:uuid)\'');
        $this->addSql('COMMENT ON COLUMN zone.parent_zone_id IS \'(DC2Type:uuid)\'');
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
        $this->addSql('ALTER TABLE capability_composition ADD CONSTRAINT FK_E109066892043242 FOREIGN KEY (capability_id) REFERENCES capability (id) NOT DEFERRABLE INITIALLY IMMEDIATE');
        $this->addSql('ALTER TABLE capability_composition_capability_action ADD CONSTRAINT FK_BAEECCCF03406B FOREIGN KEY (capability_composition_id) REFERENCES capability_composition (id) ON DELETE CASCADE NOT DEFERRABLE INITIALLY IMMEDIATE');
        $this->addSql('ALTER TABLE capability_composition_capability_action ADD CONSTRAINT FK_BAEECCC3AD9EF42 FOREIGN KEY (capability_action_id) REFERENCES capability_action (id) ON DELETE CASCADE NOT DEFERRABLE INITIALLY IMMEDIATE');
        $this->addSql('ALTER TABLE capability_composition_capability_state ADD CONSTRAINT FK_A8E3D2DAF03406B FOREIGN KEY (capability_composition_id) REFERENCES capability_composition (id) ON DELETE CASCADE NOT DEFERRABLE INITIALLY IMMEDIATE');
        $this->addSql('ALTER TABLE capability_composition_capability_state ADD CONSTRAINT FK_A8E3D2DA89E12A25 FOREIGN KEY (capability_state_id) REFERENCES capability_state (id) ON DELETE CASCADE NOT DEFERRABLE INITIALLY IMMEDIATE');
        $this->addSql('ALTER TABLE device_capability_composition ADD CONSTRAINT FK_90CA50CB94A4C7D4 FOREIGN KEY (device_id) REFERENCES device (id) ON DELETE CASCADE NOT DEFERRABLE INITIALLY IMMEDIATE');
        $this->addSql('ALTER TABLE device_capability_composition ADD CONSTRAINT FK_90CA50CBF03406B FOREIGN KEY (capability_composition_id) REFERENCES capability_composition (id) ON DELETE CASCADE NOT DEFERRABLE INITIALLY IMMEDIATE');
        $this->addSql('ALTER TABLE docker_custom_command ADD CONSTRAINT FK_EC3D7E073FC453F8 FOREIGN KEY (docker_id) REFERENCES docker (id) NOT DEFERRABLE INITIALLY IMMEDIATE');
        $this->addSql('ALTER TABLE group_device ADD CONSTRAINT FK_DAA96E5EFE54D947 FOREIGN KEY (group_id) REFERENCES "group" (id) ON DELETE CASCADE NOT DEFERRABLE INITIALLY IMMEDIATE');
        $this->addSql('ALTER TABLE group_device ADD CONSTRAINT FK_DAA96E5E94A4C7D4 FOREIGN KEY (device_id) REFERENCES device (id) ON DELETE CASCADE NOT DEFERRABLE INITIALLY IMMEDIATE');
        $this->addSql('ALTER TABLE plugin ADD CONSTRAINT FK_E96E27946BF700BD FOREIGN KEY (status_id) REFERENCES plugin_status (id) NOT DEFERRABLE INITIALLY IMMEDIATE');
        $this->addSql('ALTER TABLE protocol ADD CONSTRAINT FK_C8C0BC4C6BF700BD FOREIGN KEY (status_id) REFERENCES protocol_status (id) NOT DEFERRABLE INITIALLY IMMEDIATE');
        $this->addSql('ALTER TABLE "user" ADD CONSTRAINT FK_8D93D6496BF700BD FOREIGN KEY (status_id) REFERENCES user_status (id) NOT DEFERRABLE INITIALLY IMMEDIATE');
        $this->addSql('ALTER TABLE "user" ADD CONSTRAINT FK_8D93D649C54C8C93 FOREIGN KEY (type_id) REFERENCES user_type (id) NOT DEFERRABLE INITIALLY IMMEDIATE');
        $this->addSql('ALTER TABLE zone ADD CONSTRAINT FK_A0EBC007ED644BED FOREIGN KEY (parent_zone_id) REFERENCES zone (id) NOT DEFERRABLE INITIALLY IMMEDIATE');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('CREATE SCHEMA public');
        $this->addSql('ALTER TABLE capability_composition DROP CONSTRAINT FK_E109066892043242');
        $this->addSql('ALTER TABLE capability_composition_capability_action DROP CONSTRAINT FK_BAEECCCF03406B');
        $this->addSql('ALTER TABLE capability_composition_capability_action DROP CONSTRAINT FK_BAEECCC3AD9EF42');
        $this->addSql('ALTER TABLE capability_composition_capability_state DROP CONSTRAINT FK_A8E3D2DAF03406B');
        $this->addSql('ALTER TABLE capability_composition_capability_state DROP CONSTRAINT FK_A8E3D2DA89E12A25');
        $this->addSql('ALTER TABLE device_capability_composition DROP CONSTRAINT FK_90CA50CB94A4C7D4');
        $this->addSql('ALTER TABLE device_capability_composition DROP CONSTRAINT FK_90CA50CBF03406B');
        $this->addSql('ALTER TABLE docker_custom_command DROP CONSTRAINT FK_EC3D7E073FC453F8');
        $this->addSql('ALTER TABLE group_device DROP CONSTRAINT FK_DAA96E5EFE54D947');
        $this->addSql('ALTER TABLE group_device DROP CONSTRAINT FK_DAA96E5E94A4C7D4');
        $this->addSql('ALTER TABLE plugin DROP CONSTRAINT FK_E96E27946BF700BD');
        $this->addSql('ALTER TABLE protocol DROP CONSTRAINT FK_C8C0BC4C6BF700BD');
        $this->addSql('ALTER TABLE "user" DROP CONSTRAINT FK_8D93D6496BF700BD');
        $this->addSql('ALTER TABLE "user" DROP CONSTRAINT FK_8D93D649C54C8C93');
        $this->addSql('ALTER TABLE zone DROP CONSTRAINT FK_A0EBC007ED644BED');
        $this->addSql('DROP TABLE capability');
        $this->addSql('DROP TABLE capability_action');
        $this->addSql('DROP TABLE capability_composition');
        $this->addSql('DROP TABLE capability_composition_capability_action');
        $this->addSql('DROP TABLE capability_composition_capability_state');
        $this->addSql('DROP TABLE capability_state');
        $this->addSql('DROP TABLE device');
        $this->addSql('DROP TABLE device_capability_composition');
        $this->addSql('DROP TABLE docker');
        $this->addSql('DROP TABLE docker_custom_command');
        $this->addSql('DROP TABLE "group"');
        $this->addSql('DROP TABLE group_device');
        $this->addSql('DROP TABLE plugin');
        $this->addSql('DROP TABLE plugin_status');
        $this->addSql('DROP TABLE protocol');
        $this->addSql('DROP TABLE protocol_status');
        $this->addSql('DROP TABLE "user"');
        $this->addSql('DROP TABLE user_status');
        $this->addSql('DROP TABLE user_type');
        $this->addSql('DROP TABLE zone');
        $this->addSql('DROP TABLE messenger_messages');
    }
}

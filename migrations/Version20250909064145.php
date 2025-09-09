<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20250909064145 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('CREATE TABLE security_login_attempt (id UUID NOT NULL, user_id UUID NOT NULL, created_at_value TIMESTAMP(0) WITHOUT TIME ZONE NOT NULL, PRIMARY KEY(id))');
        $this->addSql('CREATE INDEX IDX_8128A205A76ED395 ON security_login_attempt (user_id)');
        $this->addSql('COMMENT ON COLUMN security_login_attempt.id IS \'(DC2Type:login_attempt_id)\'');
        $this->addSql('COMMENT ON COLUMN security_login_attempt.user_id IS \'(DC2Type:user_id)\'');
        $this->addSql('CREATE TABLE security_refresh_tokens (id SERIAL NOT NULL, refresh_token VARCHAR(128) NOT NULL, username VARCHAR(255) NOT NULL, valid TIMESTAMP(0) WITHOUT TIME ZONE NOT NULL, PRIMARY KEY(id))');
        $this->addSql('CREATE UNIQUE INDEX UNIQ_612A333CC74F2195 ON security_refresh_tokens (refresh_token)');
        $this->addSql('CREATE TABLE security_user (id UUID NOT NULL, status_id UUID NOT NULL, type_id UUID NOT NULL, email VARCHAR(500) NOT NULL, password VARCHAR(255) NOT NULL, firstname_value VARCHAR(255) NOT NULL, lastname_value VARCHAR(255) NOT NULL, roles_value JSON NOT NULL, created_at_value TIMESTAMP(0) WITHOUT TIME ZONE NOT NULL, updated_at_value TIMESTAMP(0) WITHOUT TIME ZONE DEFAULT NULL, PRIMARY KEY(id))');
        $this->addSql('CREATE UNIQUE INDEX UNIQ_52825A88E7927C74 ON security_user (email)');
        $this->addSql('CREATE INDEX IDX_52825A886BF700BD ON security_user (status_id)');
        $this->addSql('CREATE INDEX IDX_52825A88C54C8C93 ON security_user (type_id)');
        $this->addSql('COMMENT ON COLUMN security_user.id IS \'(DC2Type:user_id)\'');
        $this->addSql('COMMENT ON COLUMN security_user.status_id IS \'(DC2Type:user_status_id)\'');
        $this->addSql('COMMENT ON COLUMN security_user.type_id IS \'(DC2Type:user_type_id)\'');
        $this->addSql('CREATE TABLE security_user_status (id UUID NOT NULL, label_value VARCHAR(255) NOT NULL, reference_value VARCHAR(64) NOT NULL, created_at_value TIMESTAMP(0) WITHOUT TIME ZONE NOT NULL, PRIMARY KEY(id))');
        $this->addSql('COMMENT ON COLUMN security_user_status.id IS \'(DC2Type:user_status_id)\'');
        $this->addSql('CREATE TABLE security_user_type (id UUID NOT NULL, label_value VARCHAR(255) NOT NULL, reference_value VARCHAR(64) NOT NULL, created_at_value TIMESTAMP(0) WITHOUT TIME ZONE NOT NULL, PRIMARY KEY(id))');
        $this->addSql('COMMENT ON COLUMN security_user_type.id IS \'(DC2Type:user_type_id)\'');
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
        $this->addSql('ALTER TABLE security_login_attempt ADD CONSTRAINT FK_8128A205A76ED395 FOREIGN KEY (user_id) REFERENCES security_user (id) ON DELETE CASCADE NOT DEFERRABLE INITIALLY IMMEDIATE');
        $this->addSql('ALTER TABLE security_user ADD CONSTRAINT FK_52825A886BF700BD FOREIGN KEY (status_id) REFERENCES security_user_status (id) NOT DEFERRABLE INITIALLY IMMEDIATE');
        $this->addSql('ALTER TABLE security_user ADD CONSTRAINT FK_52825A88C54C8C93 FOREIGN KEY (type_id) REFERENCES security_user_type (id) NOT DEFERRABLE INITIALLY IMMEDIATE');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('CREATE SCHEMA public');
        $this->addSql('ALTER TABLE security_login_attempt DROP CONSTRAINT FK_8128A205A76ED395');
        $this->addSql('ALTER TABLE security_user DROP CONSTRAINT FK_52825A886BF700BD');
        $this->addSql('ALTER TABLE security_user DROP CONSTRAINT FK_52825A88C54C8C93');
        $this->addSql('DROP TABLE security_login_attempt');
        $this->addSql('DROP TABLE security_refresh_tokens');
        $this->addSql('DROP TABLE security_user');
        $this->addSql('DROP TABLE security_user_status');
        $this->addSql('DROP TABLE security_user_type');
        $this->addSql('DROP TABLE messenger_messages');
    }
}

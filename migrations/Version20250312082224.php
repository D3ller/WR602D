<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20250312082224 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('CREATE TABLE file (id SERIAL NOT NULL, account_id INT DEFAULT NULL, name VARCHAR(255) NOT NULL, created_at TIMESTAMP(0) WITHOUT TIME ZONE NOT NULL, PRIMARY KEY(id))');
        $this->addSql('CREATE INDEX IDX_8C9F36109B6B5FBA ON file (account_id)');
        $this->addSql('COMMENT ON COLUMN file.created_at IS \'(DC2Type:datetime_immutable)\'');
        $this->addSql('CREATE TABLE subscription (id SERIAL NOT NULL, name VARCHAR(255) NOT NULL, description TEXT NOT NULL, max_pdf INT NOT NULL, price INT NOT NULL, special_price DOUBLE PRECISION DEFAULT NULL, special_price_from DATE DEFAULT NULL, special_price_to DATE DEFAULT NULL, PRIMARY KEY(id))');
        $this->addSql('CREATE TABLE "user" (id SERIAL NOT NULL, subscription_id INT NOT NULL, email VARCHAR(180) NOT NULL, roles JSON NOT NULL, password VARCHAR(255) NOT NULL, lastname VARCHAR(255) NOT NULL, firstname VARCHAR(255) NOT NULL, PRIMARY KEY(id))');
        $this->addSql('CREATE INDEX IDX_8D93D6499A1887DC ON "user" (subscription_id)');
        $this->addSql('CREATE UNIQUE INDEX UNIQ_IDENTIFIER_EMAIL ON "user" (email)');
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
        $this->addSql('ALTER TABLE file ADD CONSTRAINT FK_8C9F36109B6B5FBA FOREIGN KEY (account_id) REFERENCES "user" (id) NOT DEFERRABLE INITIALLY IMMEDIATE');
        $this->addSql('ALTER TABLE "user" ADD CONSTRAINT FK_8D93D6499A1887DC FOREIGN KEY (subscription_id) REFERENCES subscription (id) NOT DEFERRABLE INITIALLY IMMEDIATE');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('CREATE SCHEMA public');
        $this->addSql('ALTER TABLE file DROP CONSTRAINT FK_8C9F36109B6B5FBA');
        $this->addSql('ALTER TABLE "user" DROP CONSTRAINT FK_8D93D6499A1887DC');
        $this->addSql('DROP TABLE file');
        $this->addSql('DROP TABLE subscription');
        $this->addSql('DROP TABLE "user"');
        $this->addSql('DROP TABLE messenger_messages');
    }
}

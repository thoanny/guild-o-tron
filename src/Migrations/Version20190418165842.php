<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20190418165842 extends AbstractMigration
{
    public function getDescription() : string
    {
        return '';
    }

    public function up(Schema $schema) : void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->abortIf($this->connection->getDatabasePlatform()->getName() !== 'mysql', 'Migration can only be executed safely on \'mysql\'.');

        $this->addSql('ALTER TABLE guild ADD website VARCHAR(255) DEFAULT NULL, CHANGE notary notary INT DEFAULT 0 NOT NULL, CHANGE tavern tavern INT DEFAULT 0 NOT NULL, CHANGE mine mine INT DEFAULT 0 NOT NULL, CHANGE workshop workshop INT DEFAULT 0 NOT NULL, CHANGE market market INT DEFAULT 0 NOT NULL, CHANGE arena arena INT DEFAULT 0 NOT NULL, CHANGE war_room war_room INT DEFAULT 0 NOT NULL');
    }

    public function down(Schema $schema) : void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->abortIf($this->connection->getDatabasePlatform()->getName() !== 'mysql', 'Migration can only be executed safely on \'mysql\'.');

        $this->addSql('ALTER TABLE guild DROP website, CHANGE notary notary INT DEFAULT 0, CHANGE tavern tavern INT DEFAULT 0, CHANGE mine mine INT DEFAULT 0, CHANGE workshop workshop INT DEFAULT 0, CHANGE market market INT DEFAULT 0, CHANGE arena arena INT DEFAULT 0, CHANGE war_room war_room INT DEFAULT 0');
    }
}

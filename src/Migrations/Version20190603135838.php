<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20190603135838 extends AbstractMigration
{
    public function getDescription() : string
    {
        return '';
    }

    public function up(Schema $schema) : void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->abortIf($this->connection->getDatabasePlatform()->getName() !== 'mysql', 'Migration can only be executed safely on \'mysql\'.');

        $this->addSql('CREATE TABLE guild_raid (id INT AUTO_INCREMENT NOT NULL, guild_id INT NOT NULL, user_id INT NOT NULL, data LONGTEXT NOT NULL COMMENT \'(DC2Type:json)\', updated_at DATETIME NOT NULL, INDEX IDX_B6D2C51D5F2131EF (guild_id), INDEX IDX_B6D2C51DA76ED395 (user_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci ENGINE = InnoDB');
        $this->addSql('ALTER TABLE guild_raid ADD CONSTRAINT FK_B6D2C51D5F2131EF FOREIGN KEY (guild_id) REFERENCES guild (id)');
        $this->addSql('ALTER TABLE guild_raid ADD CONSTRAINT FK_B6D2C51DA76ED395 FOREIGN KEY (user_id) REFERENCES user (id)');
    }

    public function down(Schema $schema) : void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->abortIf($this->connection->getDatabasePlatform()->getName() !== 'mysql', 'Migration can only be executed safely on \'mysql\'.');

        $this->addSql('DROP TABLE guild_raid');
    }
}

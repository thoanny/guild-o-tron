<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20190412065543 extends AbstractMigration
{
    public function getDescription() : string
    {
        return '';
    }

    public function up(Schema $schema) : void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->abortIf($this->connection->getDatabasePlatform()->getName() !== 'mysql', 'Migration can only be executed safely on \'mysql\'.');

        $this->addSql('CREATE TABLE guild_treasury (id INT AUTO_INCREMENT NOT NULL, guild_id INT NOT NULL, treasury LONGTEXT NOT NULL COMMENT \'(DC2Type:json)\', checksum VARCHAR(255) NOT NULL, updated_at DATETIME NOT NULL, UNIQUE INDEX UNIQ_B544022B5F2131EF (guild_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci ENGINE = InnoDB');
        $this->addSql('ALTER TABLE guild_treasury ADD CONSTRAINT FK_B544022B5F2131EF FOREIGN KEY (guild_id) REFERENCES guild (id)');
        $this->addSql('DROP TABLE guild_treasury_updates');
    }

    public function down(Schema $schema) : void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->abortIf($this->connection->getDatabasePlatform()->getName() !== 'mysql', 'Migration can only be executed safely on \'mysql\'.');

        $this->addSql('CREATE TABLE guild_treasury_updates (id INT AUTO_INCREMENT NOT NULL, guild_id INT NOT NULL, treasury LONGTEXT NOT NULL COLLATE utf8mb4_unicode_ci COMMENT \'(DC2Type:json)\', updates LONGTEXT NOT NULL COLLATE utf8mb4_unicode_ci COMMENT \'(DC2Type:json)\', checksum VARCHAR(255) NOT NULL COLLATE utf8mb4_unicode_ci, updated_at DATETIME NOT NULL, UNIQUE INDEX UNIQ_4E6D527A5F2131EF (guild_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8 COLLATE utf8_unicode_ci ENGINE = InnoDB COMMENT = \'\' ');
        $this->addSql('ALTER TABLE guild_treasury_updates ADD CONSTRAINT FK_4E6D527A5F2131EF FOREIGN KEY (guild_id) REFERENCES guild (id)');
        $this->addSql('DROP TABLE guild_treasury');
    }
}

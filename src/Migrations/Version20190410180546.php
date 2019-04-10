<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20190410180546 extends AbstractMigration
{
    public function getDescription() : string
    {
        return '';
    }

    public function up(Schema $schema) : void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->abortIf($this->connection->getDatabasePlatform()->getName() !== 'mysql', 'Migration can only be executed safely on \'mysql\'.');

        $this->addSql('CREATE TABLE guild (id INT AUTO_INCREMENT NOT NULL, user_id INT NOT NULL, tag VARCHAR(25) NOT NULL, name VARCHAR(255) NOT NULL, description LONGTEXT DEFAULT NULL, token VARCHAR(255) NOT NULL, slug VARCHAR(150) NOT NULL, gid VARCHAR(255) NOT NULL, UNIQUE INDEX UNIQ_75407DAB989D9B62 (slug), INDEX IDX_75407DABA76ED395 (user_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci ENGINE = InnoDB');
        $this->addSql('CREATE TABLE guild_log (id INT AUTO_INCREMENT NOT NULL, guild_id INT NOT NULL, created_at DATETIME NOT NULL, user_name VARCHAR(255) NOT NULL, type VARCHAR(255) NOT NULL, data LONGTEXT NOT NULL COMMENT \'(DC2Type:json)\', lid INT NOT NULL, INDEX IDX_D401DE8E5F2131EF (guild_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci ENGINE = InnoDB');
        $this->addSql('CREATE TABLE guild_member (id INT AUTO_INCREMENT NOT NULL, guild_id INT NOT NULL, members LONGTEXT NOT NULL COMMENT \'(DC2Type:json)\', checksum VARCHAR(255) NOT NULL, UNIQUE INDEX UNIQ_7FD58C975F2131EF (guild_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci ENGINE = InnoDB');
        $this->addSql('CREATE TABLE guild_stash (id INT AUTO_INCREMENT NOT NULL, guild_id INT NOT NULL, stash LONGTEXT NOT NULL COMMENT \'(DC2Type:json)\', checksum VARCHAR(255) NOT NULL, updated_at DATETIME NOT NULL, UNIQUE INDEX UNIQ_A3ECE3C85F2131EF (guild_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci ENGINE = InnoDB');
        $this->addSql('CREATE TABLE user (id INT AUTO_INCREMENT NOT NULL, email VARCHAR(180) NOT NULL, roles LONGTEXT NOT NULL COMMENT \'(DC2Type:json)\', password VARCHAR(255) NOT NULL, reset_token VARCHAR(255) DEFAULT NULL, UNIQUE INDEX UNIQ_8D93D649E7927C74 (email), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci ENGINE = InnoDB');
        $this->addSql('ALTER TABLE guild ADD CONSTRAINT FK_75407DABA76ED395 FOREIGN KEY (user_id) REFERENCES user (id)');
        $this->addSql('ALTER TABLE guild_log ADD CONSTRAINT FK_D401DE8E5F2131EF FOREIGN KEY (guild_id) REFERENCES guild (id)');
        $this->addSql('ALTER TABLE guild_member ADD CONSTRAINT FK_7FD58C975F2131EF FOREIGN KEY (guild_id) REFERENCES guild (id)');
        $this->addSql('ALTER TABLE guild_stash ADD CONSTRAINT FK_A3ECE3C85F2131EF FOREIGN KEY (guild_id) REFERENCES guild (id)');
    }

    public function down(Schema $schema) : void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->abortIf($this->connection->getDatabasePlatform()->getName() !== 'mysql', 'Migration can only be executed safely on \'mysql\'.');

        $this->addSql('ALTER TABLE guild_log DROP FOREIGN KEY FK_D401DE8E5F2131EF');
        $this->addSql('ALTER TABLE guild_member DROP FOREIGN KEY FK_7FD58C975F2131EF');
        $this->addSql('ALTER TABLE guild_stash DROP FOREIGN KEY FK_A3ECE3C85F2131EF');
        $this->addSql('ALTER TABLE guild DROP FOREIGN KEY FK_75407DABA76ED395');
        $this->addSql('DROP TABLE guild');
        $this->addSql('DROP TABLE guild_log');
        $this->addSql('DROP TABLE guild_member');
        $this->addSql('DROP TABLE guild_stash');
        $this->addSql('DROP TABLE user');
    }
}

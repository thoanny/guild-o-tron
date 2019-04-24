<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20190424090343 extends AbstractMigration
{
    public function getDescription() : string
    {
        return '';
    }

    public function up(Schema $schema) : void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->abortIf($this->connection->getDatabasePlatform()->getName() !== 'mysql', 'Migration can only be executed safely on \'mysql\'.');

        $this->addSql('CREATE TABLE guild_guild_activity (guild_id INT NOT NULL, guild_activity_id INT NOT NULL, INDEX IDX_54CAAC445F2131EF (guild_id), INDEX IDX_54CAAC44C7215CB0 (guild_activity_id), PRIMARY KEY(guild_id, guild_activity_id)) DEFAULT CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci ENGINE = InnoDB');
        $this->addSql('CREATE TABLE guild_activity (id INT AUTO_INCREMENT NOT NULL, fr VARCHAR(255) NOT NULL, en VARCHAR(255) NOT NULL, de VARCHAR(255) NOT NULL, es VARCHAR(255) NOT NULL, uid VARCHAR(255) NOT NULL, PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci ENGINE = InnoDB');
        $this->addSql('ALTER TABLE guild_guild_activity ADD CONSTRAINT FK_54CAAC445F2131EF FOREIGN KEY (guild_id) REFERENCES guild (id) ON DELETE CASCADE');
        $this->addSql('ALTER TABLE guild_guild_activity ADD CONSTRAINT FK_54CAAC44C7215CB0 FOREIGN KEY (guild_activity_id) REFERENCES guild_activity (id) ON DELETE CASCADE');
        $this->addSql('ALTER TABLE guild DROP activities');
    }

    public function down(Schema $schema) : void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->abortIf($this->connection->getDatabasePlatform()->getName() !== 'mysql', 'Migration can only be executed safely on \'mysql\'.');

        $this->addSql('ALTER TABLE guild_guild_activity DROP FOREIGN KEY FK_54CAAC44C7215CB0');
        $this->addSql('DROP TABLE guild_guild_activity');
        $this->addSql('DROP TABLE guild_activity');
        $this->addSql('ALTER TABLE guild ADD activities LONGTEXT DEFAULT NULL COLLATE utf8mb4_unicode_ci COMMENT \'(DC2Type:array)\'');
    }
}

<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20190424074012 extends AbstractMigration
{
    public function getDescription() : string
    {
        return '';
    }

    public function up(Schema $schema) : void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->abortIf($this->connection->getDatabasePlatform()->getName() !== 'mysql', 'Migration can only be executed safely on \'mysql\'.');

        $this->addSql('CREATE TABLE guild_guild_tag (guild_id INT NOT NULL, guild_tag_id INT NOT NULL, INDEX IDX_D2B248105F2131EF (guild_id), INDEX IDX_D2B24810B5E315FE (guild_tag_id), PRIMARY KEY(guild_id, guild_tag_id)) DEFAULT CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci ENGINE = InnoDB');
        $this->addSql('CREATE TABLE guild_tag (id INT AUTO_INCREMENT NOT NULL, fr VARCHAR(255) NOT NULL, en VARCHAR(255) NOT NULL, de VARCHAR(255) NOT NULL, es VARCHAR(255) NOT NULL, PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci ENGINE = InnoDB');
        $this->addSql('ALTER TABLE guild_guild_tag ADD CONSTRAINT FK_D2B248105F2131EF FOREIGN KEY (guild_id) REFERENCES guild (id) ON DELETE CASCADE');
        $this->addSql('ALTER TABLE guild_guild_tag ADD CONSTRAINT FK_D2B24810B5E315FE FOREIGN KEY (guild_tag_id) REFERENCES guild_tag (id) ON DELETE CASCADE');
    }

    public function down(Schema $schema) : void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->abortIf($this->connection->getDatabasePlatform()->getName() !== 'mysql', 'Migration can only be executed safely on \'mysql\'.');

        $this->addSql('ALTER TABLE guild_guild_tag DROP FOREIGN KEY FK_D2B24810B5E315FE');
        $this->addSql('DROP TABLE guild_guild_tag');
        $this->addSql('DROP TABLE guild_tag');
    }
}

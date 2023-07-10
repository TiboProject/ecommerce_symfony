<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20230615160549 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE user ADD fav_team_id INT DEFAULT NULL, DROP fav_team');
        $this->addSql('ALTER TABLE user ADD CONSTRAINT FK_8D93D649567CDE3C FOREIGN KEY (fav_team_id) REFERENCES team (id)');
        $this->addSql('CREATE INDEX IDX_8D93D649567CDE3C ON user (fav_team_id)');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE user DROP FOREIGN KEY FK_8D93D649567CDE3C');
        $this->addSql('DROP INDEX IDX_8D93D649567CDE3C ON user');
        $this->addSql('ALTER TABLE user ADD fav_team VARCHAR(255) DEFAULT NULL, DROP fav_team_id');
    }
}

<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20230601212935 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('CREATE TABLE location (id INT AUTO_INCREMENT NOT NULL, ville VARCHAR(255) NOT NULL, adresse VARCHAR(255) NOT NULL, code_postal VARCHAR(255) NOT NULL, PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE `order` (id INT AUTO_INCREMENT NOT NULL, user_id INT NOT NULL, location_id INT NOT NULL, date_order DATETIME NOT NULL, status VARCHAR(255) NOT NULL, INDEX IDX_F5299398A76ED395 (user_id), UNIQUE INDEX UNIQ_F529939864D218E (location_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE order_line (id INT AUTO_INCREMENT NOT NULL, commande_id INT NOT NULL, product_id INT NOT NULL, quantity INT NOT NULL, INDEX IDX_9CE58EE182EA2E54 (commande_id), INDEX IDX_9CE58EE14584665A (product_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE product (id INT AUTO_INCREMENT NOT NULL, name VARCHAR(255) NOT NULL, category VARCHAR(255) NOT NULL, price INT NOT NULL, PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE stock (id INT AUTO_INCREMENT NOT NULL, product_id INT NOT NULL, number INT NOT NULL, UNIQUE INDEX UNIQ_4B3656604584665A (product_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE user (id INT AUTO_INCREMENT NOT NULL, mail VARCHAR(255) NOT NULL, password VARCHAR(255) NOT NULL, username VARCHAR(255) NOT NULL, reward_points INT DEFAULT NULL, PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE messenger_messages (id BIGINT AUTO_INCREMENT NOT NULL, body LONGTEXT NOT NULL, headers LONGTEXT NOT NULL, queue_name VARCHAR(190) NOT NULL, created_at DATETIME NOT NULL, available_at DATETIME NOT NULL, delivered_at DATETIME DEFAULT NULL, INDEX IDX_75EA56E0FB7336F0 (queue_name), INDEX IDX_75EA56E0E3BD61CE (available_at), INDEX IDX_75EA56E016BA31DB (delivered_at), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('ALTER TABLE `order` ADD CONSTRAINT FK_F5299398A76ED395 FOREIGN KEY (user_id) REFERENCES user (id)');
        $this->addSql('ALTER TABLE `order` ADD CONSTRAINT FK_F529939864D218E FOREIGN KEY (location_id) REFERENCES location (id)');
        $this->addSql('ALTER TABLE order_line ADD CONSTRAINT FK_9CE58EE182EA2E54 FOREIGN KEY (commande_id) REFERENCES `order` (id)');
        $this->addSql('ALTER TABLE order_line ADD CONSTRAINT FK_9CE58EE14584665A FOREIGN KEY (product_id) REFERENCES product (id)');
        $this->addSql('ALTER TABLE stock ADD CONSTRAINT FK_4B3656604584665A FOREIGN KEY (product_id) REFERENCES product (id)');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE `order` DROP FOREIGN KEY FK_F5299398A76ED395');
        $this->addSql('ALTER TABLE `order` DROP FOREIGN KEY FK_F529939864D218E');
        $this->addSql('ALTER TABLE order_line DROP FOREIGN KEY FK_9CE58EE182EA2E54');
        $this->addSql('ALTER TABLE order_line DROP FOREIGN KEY FK_9CE58EE14584665A');
        $this->addSql('ALTER TABLE stock DROP FOREIGN KEY FK_4B3656604584665A');
        $this->addSql('DROP TABLE location');
        $this->addSql('DROP TABLE `order`');
        $this->addSql('DROP TABLE order_line');
        $this->addSql('DROP TABLE product');
        $this->addSql('DROP TABLE stock');
        $this->addSql('DROP TABLE user');
        $this->addSql('DROP TABLE messenger_messages');
    }
}

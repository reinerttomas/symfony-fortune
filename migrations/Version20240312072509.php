<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20240312072509 extends AbstractMigration
{
    public function getDescription(): string
    {
        return 'fortune_cookie';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('CREATE TABLE fortune_cookie (id INT AUTO_INCREMENT NOT NULL, category_id INT NOT NULL, fortune VARCHAR(255) NOT NULL, number_printed INT NOT NULL, discontinued TINYINT(1) NOT NULL, created_at DATETIME NOT NULL COMMENT \'(DC2Type:datetime_immutable)\', INDEX IDX_F8D8B48712469DE2 (category_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('ALTER TABLE fortune_cookie ADD CONSTRAINT FK_F8D8B48712469DE2 FOREIGN KEY (category_id) REFERENCES category (id)');
    }
}

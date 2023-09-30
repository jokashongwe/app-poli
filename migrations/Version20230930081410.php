<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20230930081410 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE membre CHANGE noidentification noidentification VARCHAR(255) DEFAULT NULL, CHANGE genre genre VARCHAR(255) DEFAULT NULL');
        $this->addSql('ALTER TABLE setting CHANGE photo_president photo_president VARCHAR(255) DEFAULT NULL');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE membre CHANGE noidentification noidentification VARCHAR(255) NOT NULL, CHANGE genre genre VARCHAR(255) NOT NULL');
        $this->addSql('ALTER TABLE setting CHANGE photo_president photo_president VARCHAR(255) NOT NULL');
    }
}

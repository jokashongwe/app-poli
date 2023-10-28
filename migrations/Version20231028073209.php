<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20231028073209 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE diffusion ADD organisation_id INT DEFAULT NULL');
        $this->addSql('ALTER TABLE diffusion ADD CONSTRAINT FK_5938415B9E6B1585 FOREIGN KEY (organisation_id) REFERENCES organisation (id)');
        $this->addSql('CREATE INDEX IDX_5938415B9E6B1585 ON diffusion (organisation_id)');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE diffusion DROP FOREIGN KEY FK_5938415B9E6B1585');
        $this->addSql('DROP INDEX IDX_5938415B9E6B1585 ON diffusion');
        $this->addSql('ALTER TABLE diffusion DROP organisation_id');
    }
}

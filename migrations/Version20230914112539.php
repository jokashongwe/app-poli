<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20230914112539 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE resultat ADD candidat_id INT DEFAULT NULL');
        $this->addSql('ALTER TABLE resultat ADD CONSTRAINT FK_E7DB5DE28D0EB82 FOREIGN KEY (candidat_id) REFERENCES candidat (id)');
        $this->addSql('CREATE INDEX IDX_E7DB5DE28D0EB82 ON resultat (candidat_id)');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE resultat DROP FOREIGN KEY FK_E7DB5DE28D0EB82');
        $this->addSql('DROP INDEX IDX_E7DB5DE28D0EB82 ON resultat');
        $this->addSql('ALTER TABLE resultat DROP candidat_id');
    }
}

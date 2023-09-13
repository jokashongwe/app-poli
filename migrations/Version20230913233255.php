<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20230913233255 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE bureau_vote ADD cironscription_id INT DEFAULT NULL, ADD code_centre VARCHAR(255) DEFAULT NULL');
        $this->addSql('ALTER TABLE bureau_vote ADD CONSTRAINT FK_82384C04D7A57EB1 FOREIGN KEY (cironscription_id) REFERENCES circonscription (id)');
        $this->addSql('CREATE INDEX IDX_82384C04D7A57EB1 ON bureau_vote (cironscription_id)');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE bureau_vote DROP FOREIGN KEY FK_82384C04D7A57EB1');
        $this->addSql('DROP INDEX IDX_82384C04D7A57EB1 ON bureau_vote');
        $this->addSql('ALTER TABLE bureau_vote DROP cironscription_id, DROP code_centre');
    }
}

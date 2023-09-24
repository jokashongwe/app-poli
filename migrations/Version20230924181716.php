<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20230924181716 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('CREATE TABLE tag (id INT AUTO_INCREMENT NOT NULL, code VARCHAR(255) NOT NULL, name VARCHAR(255) NOT NULL, UNIQUE INDEX UNIQ_389B78377153098 (code), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE tag_membre (tag_id INT NOT NULL, membre_id INT NOT NULL, INDEX IDX_1FF5B205BAD26311 (tag_id), INDEX IDX_1FF5B2056A99F74A (membre_id), PRIMARY KEY(tag_id, membre_id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('ALTER TABLE tag_membre ADD CONSTRAINT FK_1FF5B205BAD26311 FOREIGN KEY (tag_id) REFERENCES tag (id) ON DELETE CASCADE');
        $this->addSql('ALTER TABLE tag_membre ADD CONSTRAINT FK_1FF5B2056A99F74A FOREIGN KEY (membre_id) REFERENCES membre (id) ON DELETE CASCADE');
        $this->addSql('ALTER TABLE membre ADD mem_section VARCHAR(255) DEFAULT NULL, ADD point_focal VARCHAR(255) DEFAULT NULL');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE tag_membre DROP FOREIGN KEY FK_1FF5B205BAD26311');
        $this->addSql('DROP TABLE tag');
        $this->addSql('DROP TABLE tag_membre');
        $this->addSql('ALTER TABLE membre DROP mem_section, DROP point_focal');
    }
}

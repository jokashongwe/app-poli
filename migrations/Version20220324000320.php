<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20220324000320 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('CREATE TABLE cotisation (id INT AUTO_INCREMENT NOT NULL, membre_id INT DEFAULT NULL, montant NUMERIC(10, 2) NOT NULL, datepaiement DATETIME NOT NULL, INDEX IDX_AE64D2ED6A99F74A (membre_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE federation (id INT AUTO_INCREMENT NOT NULL, province_id INT NOT NULL, federation_id INT DEFAULT NULL, nom VARCHAR(255) NOT NULL, INDEX IDX_AD241BCDE946114A (province_id), INDEX IDX_AD241BCD6A03EFC5 (federation_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE membre (id INT AUTO_INCREMENT NOT NULL, federation_id INT DEFAULT NULL, nom VARCHAR(255) NOT NULL, prenom VARCHAR(255) NOT NULL, postnom VARCHAR(255) NOT NULL, datenaissance DATE NOT NULL, adresse VARCHAR(255) NOT NULL, noidentification VARCHAR(255) NOT NULL, INDEX IDX_F6B4FB296A03EFC5 (federation_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE province (id INT AUTO_INCREMENT NOT NULL, nom VARCHAR(255) NOT NULL, PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('ALTER TABLE cotisation ADD CONSTRAINT FK_AE64D2ED6A99F74A FOREIGN KEY (membre_id) REFERENCES membre (id)');
        $this->addSql('ALTER TABLE federation ADD CONSTRAINT FK_AD241BCDE946114A FOREIGN KEY (province_id) REFERENCES province (id)');
        $this->addSql('ALTER TABLE federation ADD CONSTRAINT FK_AD241BCD6A03EFC5 FOREIGN KEY (federation_id) REFERENCES federation (id)');
        $this->addSql('ALTER TABLE membre ADD CONSTRAINT FK_F6B4FB296A03EFC5 FOREIGN KEY (federation_id) REFERENCES federation (id)');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE federation DROP FOREIGN KEY FK_AD241BCD6A03EFC5');
        $this->addSql('ALTER TABLE membre DROP FOREIGN KEY FK_F6B4FB296A03EFC5');
        $this->addSql('ALTER TABLE cotisation DROP FOREIGN KEY FK_AE64D2ED6A99F74A');
        $this->addSql('ALTER TABLE federation DROP FOREIGN KEY FK_AD241BCDE946114A');
        $this->addSql('DROP TABLE cotisation');
        $this->addSql('DROP TABLE federation');
        $this->addSql('DROP TABLE membre');
        $this->addSql('DROP TABLE province');
    }
}

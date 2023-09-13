<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20230913095748 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('CREATE TABLE bureau_vote (id INT AUTO_INCREMENT NOT NULL, code VARCHAR(255) NOT NULL, address VARCHAR(255) NOT NULL, commune VARCHAR(255) DEFAULT NULL, territoire VARCHAR(255) DEFAULT NULL, PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE candidat (id INT AUTO_INCREMENT NOT NULL, membre_id INT DEFAULT NULL, categorie VARCHAR(255) NOT NULL, type_election VARCHAR(255) NOT NULL, created_at DATETIME NOT NULL COMMENT \'(DC2Type:datetime_immutable)\', regroupement VARCHAR(255) NOT NULL, parti VARCHAR(255) DEFAULT NULL, UNIQUE INDEX UNIQ_6AB5B4716A99F74A (membre_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE circonscription (id INT AUTO_INCREMENT NOT NULL, province_id INT DEFAULT NULL, code VARCHAR(255) NOT NULL, nom VARCHAR(255) DEFAULT NULL, address VARCHAR(255) DEFAULT NULL, INDEX IDX_FEDDA65AE946114A (province_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE resultat (id INT AUTO_INCREMENT NOT NULL, temoin_id INT DEFAULT NULL, nombre_votant INT DEFAULT NULL, nombre_voix INT NOT NULL, proce_verbaux JSON NOT NULL, INDEX IDX_E7DB5DE21655312C (temoin_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE temoin (id INT AUTO_INCREMENT NOT NULL, membre_id INT DEFAULT NULL, circonscription_id INT DEFAULT NULL, bureau_vote_id INT DEFAULT NULL, candidat_id INT DEFAULT NULL, accreditation VARCHAR(255) NOT NULL, UNIQUE INDEX UNIQ_E451293B6A99F74A (membre_id), INDEX IDX_E451293B755DBAE (circonscription_id), INDEX IDX_E451293B1586D5F9 (bureau_vote_id), INDEX IDX_E451293B8D0EB82 (candidat_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('ALTER TABLE candidat ADD CONSTRAINT FK_6AB5B4716A99F74A FOREIGN KEY (membre_id) REFERENCES membre (id)');
        $this->addSql('ALTER TABLE circonscription ADD CONSTRAINT FK_FEDDA65AE946114A FOREIGN KEY (province_id) REFERENCES province (id)');
        $this->addSql('ALTER TABLE resultat ADD CONSTRAINT FK_E7DB5DE21655312C FOREIGN KEY (temoin_id) REFERENCES temoin (id)');
        $this->addSql('ALTER TABLE temoin ADD CONSTRAINT FK_E451293B6A99F74A FOREIGN KEY (membre_id) REFERENCES membre (id)');
        $this->addSql('ALTER TABLE temoin ADD CONSTRAINT FK_E451293B755DBAE FOREIGN KEY (circonscription_id) REFERENCES circonscription (id)');
        $this->addSql('ALTER TABLE temoin ADD CONSTRAINT FK_E451293B1586D5F9 FOREIGN KEY (bureau_vote_id) REFERENCES bureau_vote (id)');
        $this->addSql('ALTER TABLE temoin ADD CONSTRAINT FK_E451293B8D0EB82 FOREIGN KEY (candidat_id) REFERENCES candidat (id)');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE temoin DROP FOREIGN KEY FK_E451293B1586D5F9');
        $this->addSql('ALTER TABLE temoin DROP FOREIGN KEY FK_E451293B8D0EB82');
        $this->addSql('ALTER TABLE temoin DROP FOREIGN KEY FK_E451293B755DBAE');
        $this->addSql('ALTER TABLE resultat DROP FOREIGN KEY FK_E7DB5DE21655312C');
        $this->addSql('DROP TABLE bureau_vote');
        $this->addSql('DROP TABLE candidat');
        $this->addSql('DROP TABLE circonscription');
        $this->addSql('DROP TABLE resultat');
        $this->addSql('DROP TABLE temoin');
    }
}

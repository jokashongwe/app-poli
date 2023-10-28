<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20231027133307 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('CREATE TABLE `member` (id INT AUTO_INCREMENT NOT NULL, organisation_id INT DEFAULT NULL, INDEX IDX_70E4FA789E6B1585 (organisation_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE organisation (id INT AUTO_INCREMENT NOT NULL, name VARCHAR(255) NOT NULL, description LONGTEXT DEFAULT NULL, PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('ALTER TABLE `member` ADD CONSTRAINT FK_70E4FA789E6B1585 FOREIGN KEY (organisation_id) REFERENCES organisation (id)');
        $this->addSql('ALTER TABLE federation ADD organisation_id INT DEFAULT NULL');
        $this->addSql('ALTER TABLE federation ADD CONSTRAINT FK_AD241BCD9E6B1585 FOREIGN KEY (organisation_id) REFERENCES organisation (id)');
        $this->addSql('CREATE INDEX IDX_AD241BCD9E6B1585 ON federation (organisation_id)');
        $this->addSql('ALTER TABLE user ADD organisation_id INT DEFAULT NULL');
        $this->addSql('ALTER TABLE user ADD CONSTRAINT FK_8D93D6499E6B1585 FOREIGN KEY (organisation_id) REFERENCES organisation (id)');
        $this->addSql('CREATE INDEX IDX_8D93D6499E6B1585 ON user (organisation_id)');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE federation DROP FOREIGN KEY FK_AD241BCD9E6B1585');
        $this->addSql('ALTER TABLE `member` DROP FOREIGN KEY FK_70E4FA789E6B1585');
        $this->addSql('ALTER TABLE `user` DROP FOREIGN KEY FK_8D93D6499E6B1585');
        $this->addSql('DROP TABLE `member`');
        $this->addSql('DROP TABLE organisation');
        $this->addSql('DROP INDEX IDX_AD241BCD9E6B1585 ON federation');
        $this->addSql('ALTER TABLE federation DROP organisation_id');
        $this->addSql('DROP INDEX IDX_8D93D6499E6B1585 ON `user`');
        $this->addSql('ALTER TABLE `user` DROP organisation_id');
    }
}

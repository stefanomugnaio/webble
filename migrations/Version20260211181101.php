<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20260211181101 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('CREATE TABLE devis_formation (id INT AUTO_INCREMENT NOT NULL, nom VARCHAR(150) NOT NULL, prenom VARCHAR(150) NOT NULL, email VARCHAR(255) NOT NULL, telephone VARCHAR(20) NOT NULL, adresse VARCHAR(255) NOT NULL, code_postal INT NOT NULL, ville VARCHAR(100) NOT NULL, formation VARCHAR(100) NOT NULL, date_demande DATE NOT NULL, PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE session_formation (id INT AUTO_INCREMENT NOT NULL, date_debut DATE NOT NULL, prix DOUBLE PRECISION NOT NULL, date_fin DATE NOT NULL, status VARCHAR(50) NOT NULL, PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('DROP TABLE devis_formation');
        $this->addSql('DROP TABLE session_formation');
    }
}

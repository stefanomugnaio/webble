<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

final class Version20261017194000 extends AbstractMigration
{
    public function getDescription(): string { return 'Ajoute la bibliothèque de tâches, les offres commerciales, leurs lignes et le papier à en-tête.'; }

    public function isTransactional(): bool { return false; }

    public function up(Schema $schema): void
    {
        $this->addSql('CREATE TABLE tache_offre (id INT AUTO_INCREMENT NOT NULL, libelle VARCHAR(255) NOT NULL, quantite NUMERIC(10, 2) NOT NULL, prix_unitaire NUMERIC(10, 2) NOT NULL, offerte TINYINT(1) NOT NULL, PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE offre_commerciale (id INT AUTO_INCREMENT NOT NULL, numero VARCHAR(40) NOT NULL, date_emission DATE NOT NULL COMMENT \'(DC2Type:date_immutable)\', date_validite DATE NOT NULL COMMENT \'(DC2Type:date_immutable)\', statut VARCHAR(30) NOT NULL, client_societe VARCHAR(180) DEFAULT NULL, client_nom VARCHAR(180) NOT NULL, client_email VARCHAR(180) DEFAULT NULL, client_adresse LONGTEXT DEFAULT NULL, objet VARCHAR(255) NOT NULL, introduction LONGTEXT DEFAULT NULL, conditions LONGTEXT DEFAULT NULL, taux_tva NUMERIC(5, 2) NOT NULL, papier_entete_url VARCHAR(2048) DEFAULT NULL, UNIQUE INDEX UNIQ_95EB2D66F55AE19E (numero), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE ligne_offre (id INT AUTO_INCREMENT NOT NULL, offre_id INT NOT NULL, libelle LONGTEXT NOT NULL, quantite NUMERIC(10, 2) NOT NULL, prix_unitaire NUMERIC(10, 2) NOT NULL, offerte TINYINT(1) NOT NULL, position INT NOT NULL, INDEX IDX_AC2A6C5F4CC8505A (offre_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE parametre_offre (id INT AUTO_INCREMENT NOT NULL, papier_entete_url VARCHAR(2048) DEFAULT NULL, PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('ALTER TABLE ligne_offre ADD CONSTRAINT FK_LIGNE_OFFRE FOREIGN KEY (offre_id) REFERENCES offre_commerciale (id) ON DELETE CASCADE');
    }

    public function down(Schema $schema): void
    {
        $this->addSql('ALTER TABLE ligne_offre DROP FOREIGN KEY FK_LIGNE_OFFRE');
        $this->addSql('DROP TABLE ligne_offre');
        $this->addSql('DROP TABLE offre_commerciale');
        $this->addSql('DROP TABLE tache_offre');
        $this->addSql('DROP TABLE parametre_offre');
    }
}

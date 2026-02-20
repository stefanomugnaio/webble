<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20260213203234 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE devis_formation ADD formation_id INT DEFAULT NULL, CHANGE formation devis_formation VARCHAR(100) NOT NULL');
        $this->addSql('ALTER TABLE devis_formation ADD CONSTRAINT FK_3193EBC55200282E FOREIGN KEY (formation_id) REFERENCES formation (id)');
        $this->addSql('CREATE INDEX IDX_3193EBC55200282E ON devis_formation (formation_id)');
        $this->addSql('ALTER TABLE formation ADD slug VARCHAR(150) NOT NULL');
        $this->addSql('CREATE UNIQUE INDEX UNIQ_404021BF989D9B62 ON formation (slug)');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE devis_formation DROP FOREIGN KEY FK_3193EBC55200282E');
        $this->addSql('DROP INDEX IDX_3193EBC55200282E ON devis_formation');
        $this->addSql('ALTER TABLE devis_formation DROP formation_id, CHANGE devis_formation formation VARCHAR(100) NOT NULL');
        $this->addSql('DROP INDEX UNIQ_404021BF989D9B62 ON formation');
        $this->addSql('ALTER TABLE formation DROP slug');
    }
}

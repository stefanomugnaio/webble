<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20260211181831 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE devis_formation ADD session_formation_id INT DEFAULT NULL');
        $this->addSql('ALTER TABLE devis_formation ADD CONSTRAINT FK_3193EBC59C9D95AF FOREIGN KEY (session_formation_id) REFERENCES session_formation (id)');
        $this->addSql('CREATE INDEX IDX_3193EBC59C9D95AF ON devis_formation (session_formation_id)');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE devis_formation DROP FOREIGN KEY FK_3193EBC59C9D95AF');
        $this->addSql('DROP INDEX IDX_3193EBC59C9D95AF ON devis_formation');
        $this->addSql('ALTER TABLE devis_formation DROP session_formation_id');
    }
}

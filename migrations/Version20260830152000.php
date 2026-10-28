<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

final class Version20260830152000 extends AbstractMigration
{
    public function getDescription(): string
    {
        return 'Ajoute le créneau librement souhaité aux demandes de formation.';
    }

    public function up(Schema $schema): void
    {
        $this->addSql('ALTER TABLE devis_formation ADD date_souhaitee DATETIME DEFAULT NULL');
    }

    public function down(Schema $schema): void
    {
        $this->addSql('ALTER TABLE devis_formation DROP date_souhaitee');
    }
}

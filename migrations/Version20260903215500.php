<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

final class Version20260903215500 extends AbstractMigration
{
    public function getDescription(): string
    {
        return 'Répare la colonne CGV manquante sur les bases de production existantes.';
    }

    public function up(Schema $schema): void
    {
        if (!$schema->getTable('devis')->hasColumn('cgv')) {
            $this->addSql('ALTER TABLE devis ADD cgv TINYINT(1) DEFAULT NULL');
        }
    }

    public function down(Schema $schema): void
    {
        if ($schema->getTable('devis')->hasColumn('cgv')) {
            $this->addSql('ALTER TABLE devis DROP cgv');
        }
    }
}

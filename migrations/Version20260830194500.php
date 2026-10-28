<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

final class Version20260830194500 extends AbstractMigration
{
    public function getDescription(): string
    {
        return 'Ajoute les journées de disponibilité sélectionnées par l’administrateur.';
    }

    public function up(Schema $schema): void
    {
        $this->addSql("CREATE TABLE disponibilite_formation (id INT AUTO_INCREMENT NOT NULL, date DATE NOT NULL COMMENT '(DC2Type:date_immutable)', UNIQUE INDEX uniq_disponibilite_formation_date (date), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB");
    }

    public function down(Schema $schema): void
    {
        $this->addSql('DROP TABLE disponibilite_formation');
    }
}

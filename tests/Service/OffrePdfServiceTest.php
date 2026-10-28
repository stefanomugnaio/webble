<?php

namespace App\Tests\Service;

use App\Entity\LigneOffre;
use App\Entity\OffreCommerciale;
use App\Service\OffrePdfService;
use PHPUnit\Framework\TestCase;
use Twig\Environment;
use Twig\Loader\FilesystemLoader;

class OffrePdfServiceTest extends TestCase
{
    public function testGenererRetourneUnPdfAvecLesLignesEtLesTotaux(): void
    {
        $twig = new Environment(new FilesystemLoader(dirname(__DIR__, 2).'/templates'));
        $service = new OffrePdfService($twig);

        $offre = (new OffreCommerciale())
            ->setNumero('OFF-TEST-001')
            ->setClientNom('Client de test')
            ->setObjet('Création d’un site vitrine')
            ->setTauxTva(20);
        $offre->addLigne((new LigneOffre())->setLibelle('Conception et intégration')->setQuantite(2)->setPrixUnitaire(100));
        $offre->addLigne((new LigneOffre())->setLibelle('Accompagnement offert')->setQuantite(1)->setPrixUnitaire(50)->setOfferte(true));

        $pdf = $service->generer($offre);

        self::assertStringStartsWith('%PDF-', $pdf);
        self::assertGreaterThan(1000, strlen($pdf));
        self::assertSame(200.0, $offre->getTotalHt());
        self::assertSame(240.0, $offre->getTotalTtc());
    }
}

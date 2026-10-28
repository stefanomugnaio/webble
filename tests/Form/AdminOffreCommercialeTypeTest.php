<?php

namespace App\Tests\Form;

use App\Entity\OffreCommerciale;
use App\Form\AdminOffreCommercialeType;
use Symfony\Bundle\FrameworkBundle\Test\KernelTestCase;
use Symfony\Component\Form\FormFactoryInterface;

class AdminOffreCommercialeTypeTest extends KernelTestCase
{
    public function testLesLignesSoumisesRestentModifiablesEtCalculentLeTotal(): void
    {
        self::bootKernel();
        $factory = self::getContainer()->get(FormFactoryInterface::class);
        $offre = (new OffreCommerciale())->setNumero('OFF-FORM-001');
        $form = $factory->create(AdminOffreCommercialeType::class, $offre, ['csrf_protection' => false]);

        $form->submit([
            'numero' => 'OFF-FORM-001', 'statut' => 'brouillon',
            'dateEmission' => '2026-10-17', 'dateValidite' => '2026-11-16',
            'clientSociete' => '', 'clientNom' => 'Client test', 'clientEmail' => 'client@example.com',
            'clientAdresse' => '1 rue de la Paix', 'objet' => 'Offre test', 'introduction' => '',
            'tauxTva' => '20', 'conditions' => '',
            'lignes' => [[
                'libelle' => 'Tâche personnalisée', 'quantite' => '3', 'prixUnitaire' => '40',
                'position' => '0',
            ]],
        ]);

        self::assertTrue($form->isSynchronized());
        self::assertTrue($form->isValid(), (string) $form->getErrors(true));
        self::assertCount(1, $offre->getLignes());
        self::assertSame('Tâche personnalisée', $offre->getLignes()->first()->getLibelle());
        self::assertSame(120.0, $offre->getTotalHt());
        self::assertSame(144.0, $offre->getTotalTtc());
    }
}

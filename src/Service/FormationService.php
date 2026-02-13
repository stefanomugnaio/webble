<?php

namespace App\Service;

use InvalidArgumentException;
use App\Entity\DevisFormation;
use Doctrine\ORM\EntityManagerInterface;

class FormationService
{
    public function __construct(
        private EntityManagerInterface $entityManager
    ) {}

    /**
     * Récupération des données de formation depuis la page pricing
     */
    public function recupererDonneesFormation(string $codeFormation): array
    {
        $formations = [

            'decouverte_informatique' => [
                'libelle' => 'Découverte informatique',
                'description' => '',
                'caracteristiques' => [
                ],
                'prix_formation_ht' => 250,
            ]        
        ];

        if (!isset($formations[$codeFormation])) {
            throw new InvalidArgumentException('Formation inconnue.');
        }

        return $formations[$codeFormation];
    }

    /**
     * Calcul des montants pour le récapitulatif
     */
    public function calculerMontants(array $formation, float $tauxTva): array
    {
        $totalHt = $formation['prix_formation_ht'];
        $tva = $totalHt * $tauxTva;

        return [
            'formation_ht' => $formation['prix_formation_ht'],
            'total_tva' => $tva,
            'total_ttc' => $totalHt + $tva,
        ];
    }

    /**
     * Enregistrement du devis
     */
    public function enregistrerDevisFormation(DevisFormation $devisFormation,string $libelleFormation): bool
    {
        // sécurité
        $devisFormation->setFormation($libelleFormation);

        $this->entityManager->persist($devisFormation);
        $this->entityManager->flush();
        
        return true;
    }
}

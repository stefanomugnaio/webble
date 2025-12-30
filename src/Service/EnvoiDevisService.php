<?php

namespace App\Service;

use App\Entity\Devis;
use Doctrine\ORM\EntityManagerInterface;
use InvalidArgumentException;

class EnvoiDevisService
{
    public function __construct(
        private EntityManagerInterface $entityManager
    ) {}

    /**
     * Récupération des données d’offre depuis la page pricing
     */
    public function recupererDonneesOffre(string $codeOffre): array
    {
        $offres = [

            'tranquille' => [
                'libelle' => 'Tranquille',
                'description' => 'Pour avoir enfin un vrai site professionnel.',
                'caracteristiques' => [
                    '1 page principale (landing page)',
                    'Design responsive (mobile, tablette, desktop)',
                    'Intégration de votre logo et de vos couleurs',
                    'Livraison clé en main, prête à être mise en ligne',
                ],
                'prix_site_ht' => 150,
                'prix_maintenance_annuelle' => 360,
            ],

            'serieuse' => [
                'libelle' => 'Sérieuse',
                'description' => 'La formule idéale pour un site solide et évolutif.',
                'caracteristiques' => [
                    'Tout ce qui est inclus dans l’offre Tranquille',
                    'Jusqu’à 5 pages',
                    'Optimisation de base pour le référencement (SEO)',
                    'Intégration des réseaux sociaux',
                    'Formation rapide à la prise en main',
                    'Support pendant 3 mois après la mise en ligne',
                ],
                'prix_site_ht' => 550,
                'prix_maintenance_annuelle' => 360,
            ],
        ];

        if (!isset($offres[$codeOffre])) {
            throw new InvalidArgumentException('Offre inconnue.');
        }

        return $offres[$codeOffre];
    }

    /**
     * Calcul des montants pour le récapitulatif
     */
    public function calculerMontants(array $offre, float $tauxTva): array
    {
        $totalHt = $offre['prix_site_ht'];
        $tva = $totalHt * $tauxTva;

        return [
            'site_ht' => $offre['prix_site_ht'],
            'maintenance_annuelle' => $offre['prix_maintenance_annuelle'],
            'total_tva' => $tva,
            'total_ttc' => $totalHt + $tva,
        ];
    }

    /**
     * Enregistrement du devis
     */
    public function enregistrerDevis(Devis $devis,string $libelleOffre): bool
    {
        // sécurité
        $devis->setOffre($libelleOffre);

        if ($devis->isContratMaintenance() === null) {
            $devis->setContratMaintenance(false);
        }

        $this->entityManager->persist($devis);
        $this->entityManager->flush();
        
        return true;
    }
}

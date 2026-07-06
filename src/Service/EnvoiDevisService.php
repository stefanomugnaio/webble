<?php

namespace App\Service;

use App\Config\AppConfig;
use App\Entity\Devis;
use Doctrine\ORM\EntityManagerInterface;
use InvalidArgumentException;

class EnvoiDevisService
{
    public function __construct(
        private EntityManagerInterface $entityManager
    ) {}

    public function recupererDonneesOffre(string $codeOffre): array
    {
        if (!isset(AppConfig::OFFRES[$codeOffre])) {
            throw new InvalidArgumentException('Offre inconnue.');
        }

        $offre = AppConfig::OFFRES[$codeOffre];

        $prixSiteHt = $offre['prix_site_ht'] ?? null;
        $pourcentageReduction = $offre['pourcentage_reduction'] ?? 0;

        $offre['code'] = $codeOffre;
        $offre['prix_site_ht_avant_reduction'] = $prixSiteHt;

        if ($prixSiteHt !== null) {
            $offre['prix_site_ht'] = $this->calculerPrixApresReduction(
                (float) $prixSiteHt,
                (float) $pourcentageReduction
            );
        }

        $moisOfferts = (int) ($offre['webble_plus_mois_offerts'] ?? 0);

        $offre['webble_plus_libelle'] = AppConfig::WEBBLE_PLUS['libelle'];
        $offre['webble_plus_description'] = AppConfig::WEBBLE_PLUS['description'];
        $offre['webble_plus_prix_mensuel_ht'] = $this->arrondirMontant((float) AppConfig::WEBBLE_PLUS['prix_mensuel_ht']);
        $offre['webble_plus_mois_offerts'] = $moisOfferts;
        $offre['webble_plus_premier_mois_facture'] = $moisOfferts + 1;

        $offre['webble_plus_total_ht'] = 0.00;
        $offre['prix_maintenance_annuelle'] = 0.00;

        return $offre;
    }

    public function recupererDonneesOffres(array $codesOffres): array
    {
        $offres = [];

        foreach ($codesOffres as $codeOffre) {
            $offres[$codeOffre] = $this->recupererDonneesOffre($codeOffre);
        }

        return $offres;
    }

    public function recupererOptionsDevis(): array
    {
        return AppConfig::OPTIONS_DEVIS;
    }

    public function calculerMontants(array $offre, ?float $tauxTva = null): array
    {
        $tauxTva ??= AppConfig::TAUX_TVA;

        $prixSiteHt = $offre['prix_site_ht'] ?? null;

        if ($prixSiteHt === null) {
            return [
                'site_ht' => null,
                'webble_plus_mensuel_ht' => $this->arrondirMontant($offre['webble_plus_prix_mensuel_ht'] ?? 0),
                'webble_plus_total_ht' => 0.00,
                'maintenance_annuelle' => 0.00,
                'total_tva' => 0.00,
                'total_ttc' => null,
            ];
        }

        $prixSiteHt = (float) $prixSiteHt;
        $totalTva = $prixSiteHt * $tauxTva;
        $totalTtc = $prixSiteHt + $totalTva;

        return [
            'site_ht' => $this->arrondirMontant($prixSiteHt),
            'webble_plus_mensuel_ht' => $this->arrondirMontant($offre['webble_plus_prix_mensuel_ht'] ?? 0),
            'webble_plus_total_ht' => 0.00,
            'maintenance_annuelle' => 0.00,
            'total_tva' => $this->arrondirMontant($totalTva),
            'total_ttc' => $this->arrondirMontant($totalTtc),
        ];
    }

    public function preparerConfigurationRecapitulatif(
        array $offre,
        array $optionsDevis,
        ?float $tauxTva = null
    ): array {
        $tauxTva ??= AppConfig::TAUX_TVA;

        $siteHt = $this->arrondirMontant((float) ($offre['prix_site_ht'] ?? 0));

        $domainePrixHt = $this->arrondirMontant($optionsDevis['nom_domaine']['prix_ht'] ?? 0);
        $hebergementPrixHt = $this->arrondirMontant($optionsDevis['hebergement']['prix_ht'] ?? 0);

        $domaineActifParDefaut = (bool) ($optionsDevis['nom_domaine']['active_par_defaut'] ?? false);
        $hebergementActifParDefaut = (bool) ($optionsDevis['hebergement']['active_par_defaut'] ?? false);

        $totalCreationHt = $siteHt;

        if ($domaineActifParDefaut) {
            $totalCreationHt += $domainePrixHt;
        }

        if ($hebergementActifParDefaut) {
            $totalCreationHt += $hebergementPrixHt;
        }

        $totalCreationTva = $totalCreationHt * $tauxTva;
        $totalCreationTtc = $totalCreationHt + $totalCreationTva;

        return [
            'site_ht' => $siteHt,
            'taux_tva' => $tauxTva,

            'offre' => [
                'code' => $offre['code'] ?? null,
                'libelle' => $offre['libelle'] ?? '',
            ],

            'webble_plus' => [
                'libelle' => $offre['webble_plus_libelle'] ?? AppConfig::WEBBLE_PLUS['libelle'],
                'description' => $offre['webble_plus_description'] ?? AppConfig::WEBBLE_PLUS['description'],
                'prix_mensuel_ht' => $this->arrondirMontant($offre['webble_plus_prix_mensuel_ht'] ?? 0),
                'mois_offerts' => (int) ($offre['webble_plus_mois_offerts'] ?? 0),
                'premier_mois_facture' => (int) ($offre['webble_plus_premier_mois_facture'] ?? 1),
                'inclus_dans_total_creation' => false,
            ],

            'options' => [
                'domaine' => [
                    'libelle' => $optionsDevis['nom_domaine']['libelle'] ?? 'Nom de domaine',
                    'description' => $optionsDevis['nom_domaine']['description'] ?? '',
                    'prix_ht' => $domainePrixHt,
                    'active_par_defaut' => $domaineActifParDefaut,
                ],

                'hebergement' => [
                    'libelle' => $optionsDevis['hebergement']['libelle'] ?? 'Hébergement',
                    'description' => $optionsDevis['hebergement']['description'] ?? '',
                    'prix_ht' => $hebergementPrixHt,
                    'active_par_defaut' => $hebergementActifParDefaut,
                ],
            ],

            'totaux_par_defaut' => [
                'total_ht' => $this->arrondirMontant($totalCreationHt),
                'total_tva' => $this->arrondirMontant($totalCreationTva),
                'total_ttc' => $this->arrondirMontant($totalCreationTtc),
            ],
        ];
    }

    public function enregistrerDevis(Devis $devis, string $libelleOffre): bool
    {
        $devis->setOffre($libelleOffre);

        if ($devis->isContratMaintenance() === null) {
            $devis->setContratMaintenance(false);
        }

        $this->entityManager->persist($devis);
        $this->entityManager->flush();

        return true;
    }

    private function calculerPrixApresReduction(float $prixHt, float $pourcentageReduction): float
    {
        if ($pourcentageReduction <= 0) {
            return $this->arrondirMontant($prixHt);
        }

        $montantReduction = $prixHt * $pourcentageReduction;
        $prixFinalHt = $prixHt - $montantReduction;

        return $this->arrondirMontant($prixFinalHt);
    }

    private function arrondirMontant(float $montant): float
    {
        return round($montant, 2);
    }
}

<?php

namespace App\Controller;

use App\Entity\Devis;
use App\Form\DevisType;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;

final class EnvoiDevisController extends AbstractController
{
    #[Route('/devis/{offre}', name: 'app_devis')]
    public function index(string $offre, Request $request, EntityManagerInterface $em): Response
    {
        // Les 3 offres du site, avec prix HT
        $offers = [
            'tranquille' => [
                'code'       => 'tranquille',
                'label'      => 'Tranquille',
                'price_ht'   => 150.0,
                'description'=> "Pour enfin avoir un vrai site (et plus juste une page Facebook).",
                'features'   => [
                    "1 page principale (landing page)",
                    "Design responsive (mobile, tablette, desktop)",
                    "Intégration de votre logo et de vos couleurs",
                    "Livraison clé en main, prête à être mise en ligne",
                ],
            ],
            'serieuse' => [
                'code'       => 'serieuse',
                'label'      => 'Sérieuse',
                'price_ht'   => 550.0,
                'description'=> "Pour ceux qui en ont marre que le site du voisin soit mieux que le leur.",
                'features'   => [
                    "Tout ce qui est inclus dans Tranquille",
                    "Jusqu’à 5 pages (Accueil, Services, À propos, Contact, etc.)",
                    "Optimisation de base pour le référencement (SEO)",
                    "Intégration de vos réseaux sociaux",
                    "Formation rapide à la prise en main du site",
                    "Espace administrateur basique pour la gestion du contenu",
                    "Support pendant 3 mois après la mise en ligne",
                ],
            ],
            'pro' => [
                'code'       => 'pro',
                'label'      => 'Pro',
                'price_ht'   => null, // sur devis
                'description'=> "Pour les idées qui ne rentrent dans aucune case (et c’est très bien comme ça).",
                'features'   => [
                    "Nombre de pages et structure adaptés à votre activité",
                    "Fonctionnalités spécifiques (blog, espace client, réservation, etc.)",
                    "Devis détaillé et transparent avant lancement",
                    "Optimisation de base pour le référencement (SEO)",
                    "Espace administrateur avancé pour la gestion du contenu",
                    "Formation à la prise en main du site",
                    "Support jusqu’à 6 mois après la mise en ligne",
                ],
            ],
        ];

        // Offre inconnue -> retour à la page formules
        if (!isset($offers[$offre])) {
            return $this->redirectToRoute('app_creation_site');
        }

        $offer = $offers[$offre];

        // TVA normale en France pour ce type de service : 20 %
        $tauxTva = 0.20;

        $montantHt  = $offer['price_ht'];  // prix du site seul
        $montantTva = null;
        $montantTtc = null;

        if ($montantHt !== null) {
            $montantTva = $montantHt * $tauxTva;
            $montantTtc = $montantHt + $montantTva;
        }

        // Prix de la maintenance : 15 € HT / mois -> 180 € HT / an
        $maintenanceHtAn = 30.0 * 12;

        // Entité Devis : infos de contact + offre choisie
        $devis = new Devis();
        $devis->setOffre($offre);

        // Formulaire
        $form = $this->createForm(DevisType::class, $devis);
        $form->handleRequest($request);

        // On calcule les montants en tenant compte de l'option maintenance
        $contratMaintenance = $devis->isContratMaintenance() ?? false;

        $maintenance_ht   = null;
        $maintenance_tva  = null;
        $maintenance_ttc  = null;
        $montant_ht_total = null;
        $montant_tva_total = null;
        $montant_ttc_total = null;

        if ($montantHt !== null) {
            // base : site seul
            $maintenance_ht  = $contratMaintenance ? $maintenanceHtAn : 0.0;
            $maintenance_tva = $maintenance_ht * $tauxTva;
            $maintenance_ttc = $maintenance_ht + $maintenance_tva;

            $montant_ht_total  = $montantHt + $maintenance_ht;
            $montant_tva_total = $montant_ht_total * $tauxTva;
            $montant_ttc_total = $montant_ht_total + $montant_tva_total;
        }

        if ($form->isSubmitted() && $form->isValid()) {
            $em->persist($devis);
            $em->flush();

            return $this->redirectToRoute('app_devis_confirmation', [
                'offre' => $offre,
            ]);
        }

        return $this->render('envoi_devis/index.html.twig', [
            'offer'              => $offer,
            'taux_tva'           => $tauxTva,
            'montant_ht'         => $montantHt,
            'montant_tva'        => $montantTva,
            'montant_ttc'        => $montantTtc,
            'maintenance_ht'     => $maintenance_ht,
            'maintenance_ttc'    => $maintenance_ttc,
            'montant_ht_total'   => $montant_ht_total,
            'montant_tva_total'  => $montant_tva_total,
            'montant_ttc_total'  => $montant_ttc_total,
            'maintenance_ht_an'  => $maintenanceHtAn,
            'contrat_maintenance'=> $contratMaintenance,
            'form'               => $form->createView(),
        ]);
    }

    #[Route('/devis/{offre}/confirmation', name: 'app_devis_confirmation')]
    public function confirmation(string $offre): Response
    {
        // Tableau simplifié pour l'affichage sur la page de confirmation
        $offers = [
            'tranquille' => [
                'code'     => 'tranquille',
                'label'    => 'Tranquille',
                'price_ht' => 150.0,
            ],
            'serieuse' => [
                'code'     => 'serieuse',
                'label'    => 'Sérieuse',
                'price_ht' => 550.0,
            ],
            'pro' => [
                'code'     => 'pro',
                'label'    => 'Pro',
                'price_ht' => null,
            ],
        ];

        if (!isset($offers[$offre])) {
            return $this->redirectToRoute('app_creation_site');
        }

        $offer = $offers[$offre];

        return $this->render('envoi_devis/confirmation.html.twig', [
            'offer' => $offer,
        ]);
    }
}

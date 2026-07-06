<?php

namespace App\Controller;

use App\Config\AppConfig;
use App\Service\EnvoiDevisService;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;

final class CreationSiteController extends AbstractController
{
    #[Route('/creation-site', name: 'app_creation_site')]
    public function index(EnvoiDevisService $devisService): Response
    {
        $codesOffres = [
            'tranquille',
            'serieuse',
            'pro',
        ];

        $offres = [];

        foreach ($codesOffres as $codeOffre) {
            $donneesOffre = $devisService->recupererDonneesOffre($codeOffre);

            $offres[$codeOffre] = [
                'donnees' => $donneesOffre,
                'montants' => $devisService->calculerMontants(
                    $donneesOffre,
                    AppConfig::TAUX_TVA
                ),
            ];
        }

        return $this->render('creation_site/index.html.twig', [
            'controller_name' => 'CreationSiteController',

            /*
             * Nouvelle structure propre utilisée par le template.
             */
            'offres' => $offres,

            /*
             * Prestations complémentaires depuis AppConfig.
             */
            'prestationsComplementaires' => AppConfig::PRESTATIONS_COMPLEMENTAIRES,

            /*
             * Anciennes variables conservées temporairement.
             */
            'donneesOffreTranquille' => $offres['tranquille']['donnees'],
            'montantsOffreTranquille' => $offres['tranquille']['montants'],

            'donneesOffreSerieuse' => $offres['serieuse']['donnees'],
            'montantsOffreSerieuse' => $offres['serieuse']['montants'],

            'donneesOffrePro' => $offres['pro']['donnees'],
            'montantsOffrePro' => $offres['pro']['montants'],
        ]);
    }
}
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
        
        $donneesOffreTranquille = $devisService->recupererDonneesOffre("tranquille");

        $montantsOffreTranquille = $devisService->calculerMontants(
            $donneesOffreTranquille,
            AppConfig::TAUX_TVA
        );

        $donneesOffreSerieuse = $devisService->recupererDonneesOffre("serieuse");

        $montantsOffreSerieuse = $devisService->calculerMontants(
            $donneesOffreSerieuse,
            AppConfig::TAUX_TVA
        );


        return $this->render('creation_site/index.html.twig', [
            'controller_name' => 'CreationSiteController',
            'montantsOffreTranquille' => $montantsOffreTranquille,
            'montantsOffreSerieuse' => $montantsOffreSerieuse
        ]);
    }
}

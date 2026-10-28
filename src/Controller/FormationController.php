<?php

namespace App\Controller;

use App\Service\FormationService;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;

final class FormationController extends AbstractController
{
    /*
    |--------------------------------------------------------------------------
    | LISTE DES FORMATIONS
    |--------------------------------------------------------------------------
    */

    #[Route('/formations', name: 'app_liste_formations')]
    public function index(
        FormationService $formationService
    ): Response
    {
        $formations = $formationService->recupererToutesLesFormations();

        return $this->render('formation/index.html.twig', [
            'formations' => $formations,
        ]);
    }

    

    /*
    |--------------------------------------------------------------------------
    | PAGE DÉTAIL FORMATION (exemple statique)
    |--------------------------------------------------------------------------
    */

    #[Route('/decouverte-informatique', name: 'app_decouverte_informatique')]
    public function decouverteInformatique(): Response
    {
        return $this->render('formation/decouverte_informatique.html.twig');
    }

    #[Route('/decouverte-mobile', name: 'app_decouverte_mobile')]
    public function decouverteMobile(): Response
    {
        return $this->render('formation/decouverte_mobile.html.twig');
    }

    /*
    |--------------------------------------------------------------------------
    | PLANNING FORMATIONS
    |--------------------------------------------------------------------------
    */

    #[Route('/formations/planning', name: 'app_planning_formations')]
    public function planningFormation(): Response
    {
        return $this->redirectToRoute('app_planning');
    }



    /*
    |--------------------------------------------------------------------------
    | AJOUT D'UNE SESSION (ADMIN)
    |--------------------------------------------------------------------------
    */

    #[Route('/admin/formation/ajouter-session', name: 'app_ajouter_session')]
    public function ajouterSessionFormation(): Response
    {
        $this->addFlash('info', 'Cliquez sur les journées où vous êtes disponible.');
        return $this->redirectToRoute('admin_planning');
    }

    #[Route('/admin/formation/ajouter', name: 'app_ajouter_formation')]
    public function ajouterFormation(): Response
    {
        return $this->redirectToRoute('admin_formation_new');
    }
}

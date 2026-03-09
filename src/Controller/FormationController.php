<?php

namespace App\Controller;

use App\Entity\Formation;
use App\Form\FormationType;
use App\Entity\SessionFormation;
use App\Service\FormationService;
use App\Form\SessionFormationType;
use App\Service\SessionFormationService;
use Symfony\Component\HttpFoundation\Request;
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
        return $this->render('formation/liste_session_formation.html.twig');
    }



    /*
    |--------------------------------------------------------------------------
    | AJOUT D'UNE SESSION (ADMIN)
    |--------------------------------------------------------------------------
    */

    #[Route('/admin/formation/ajouter-session', name: 'app_ajouter_session')]
    public function ajouterSessionFormation(Request $request, SessionFormationService $sessionFormationService): Response
    {
        $session = new SessionFormation();

        $formulaire = $this->createForm(
            SessionFormationType::class,
            $session
        );

        $formulaire->handleRequest($request);

        if ($formulaire->isSubmitted() && $formulaire->isValid()) {

            $sessionFormationService->creerSession($session);

            $this->addFlash(
                'success',
                'La session de formation a été créée avec succès.'
            );

            return $this->redirectToRoute('app_liste_formations');
        }

        return $this->render('formation/ajouter_session_formation.html.twig', [
            'form' => $formulaire,
        ]);
    }

    #[Route('/admin/formation/ajouter', name: 'app_ajouter_formation')]
    public function ajouterFormation(
        Request $request,
        FormationService $formationService
    ): Response
    {
        $formation = new Formation();

        $formulaire = $this->createForm(
            FormationType::class,
            $formation
        );

        $formulaire->handleRequest($request);

        if ($formulaire->isSubmitted() && $formulaire->isValid()) {

         
            $slug = strtolower(
                trim(
                    preg_replace('/[^A-Za-z0-9-]+/', '-', $formation->getLibelle())
                )
            );

            $formation->setSlug($slug);
            dd($formation);
            $formationService->enregistrerFormation($formation);

            $this->addFlash(
                'success',
                'La formation a été ajoutée avec succès.'
            );

            return $this->redirectToRoute('app_liste_formations');
        }

        return $this->render('formation/ajouter_formation.html.twig', [
            'form' => $formulaire,
        ]);
    }
}

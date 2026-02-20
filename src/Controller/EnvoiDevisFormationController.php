<?php

namespace App\Controller;

use App\Entity\DevisFormation;
use App\Form\DevisFormationType;
use App\Service\EnvoiDevisFormationService;
use App\Service\NotificationEmailService;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;

class EnvoiDevisFormationController extends AbstractController
{
    #[Route('/formation/{formation}', name: 'app_formation')]
    public function formation(
        string $formation,
        Request $request,
        EnvoiDevisFormationService $formationService,
        NotificationEmailService $notificationEmailService
    ): Response
    {
        // 1. Données de la formation (comme recupererDonneesOffre)
        $donneesFormation = $formationService->recupererDonneesFormation($formation);

        // 2. Montants
        $montants = $formationService->calculerMontants(
            $donneesFormation,
            0.20
        );

        // 3. Formulaire
        $devisFormation = new DevisFormation();
        $form = $this->createForm(DevisFormationType::class, $devisFormation);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {

            if ($formationService->enregistrerDemande($devisFormation,$donneesFormation->getLibelle())) {
                // $notificationEmailService->envoyerNotificationFormation($donneesFormation['libelle']);
            }

            return $this->render('formation/confirmation.html.twig', [
                'formation' => $donneesFormation,
                'devisFormation' => $devisFormation,
            ]);
        }

        return $this->render('formation/devis_formation.html.twig', [
            'form' => $form,
            'formation' => $donneesFormation,
            'montants' => $montants,
            'taux_tva' => 0.20,
        ]);
    }
}
<?php

namespace App\Controller;

use App\Entity\Devis;
use App\Form\DevisType;
use App\Entity\DevisFormation;
use App\Form\DevisFormationType;
use App\Service\FormationService;
use App\Service\NotificationEmailService;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;

final class FormationController extends AbstractController
{
    #[Route('/formations', name: 'app_liste_formations')]
        public function index(): Response
        {
            return $this->render('formation/index.html.twig');
        }

    #[Route('/decouverte-informatique', name: 'app_decouverte_informatique')]
    public function decouverteInformatique(): Response
    {
        return $this->render('formation/decouverte_informatique.html.twig');
    }

    #[Route('/formation/devis/{formation}', name: 'app_formation')]
    public function devisFormation(
        string $formation,
        Request $request,
        FormationService $formationService,
        NotificationEmailService $notificationEmailService
    ): Response
    {
        $donneesFormation = $formationService->recupererDonneesFormation($formation);

        $montants = $formationService->calculerMontants(
            $donneesFormation,
            0.20
        );

        $devisFormation = new DevisFormation();

        // ✅ On injecte les données ici (pas dans le form)
        $devisFormation->setFormation($donneesFormation['libelle']);
        $devisFormation->setDateDemande(new \DateTime());

        $form = $this->createForm(
            DevisFormationType::class,
            $devisFormation
        );

        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {

            $formationService->enregistrerDevisFormation(
                $devisFormation,
                $donneesFormation['libelle']
            );

            $notificationEmailService->envoyerNotificationDevis(
                $devisFormation->getFormation()
            );

            return $this->render('envoi_devis/confirmation.html.twig', [
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

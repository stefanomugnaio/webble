<?php

namespace App\Controller;

use App\Config\AppConfig;
use App\Entity\Devis;
use App\Form\DevisType;
use App\Service\EnvoiDevisService;
use App\Service\NotificationEmailService;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;

class EnvoiDevisController extends AbstractController
{
    #[Route('/devis/{offre}', name: 'app_devis')]
    public function devis(
        string $offre,
        Request $request,
        EnvoiDevisService $devisService,
        NotificationEmailService $notificationEmailService
    ): Response
    {
        $donneesOffre = $devisService->recupererDonneesOffre($offre);

        $montants = $devisService->calculerMontants(
            $donneesOffre,
            AppConfig::TAUX_TVA
        );


        $devis = new Devis();
        $form = $this->createForm(DevisType::class, $devis);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            if ($devisService->enregistrerDevis($devis,$donneesOffre['libelle'])){
                $notificationEmailService->envoyerNotificationDevis($form->getData());
            }

            return $this->render('envoi_devis/confirmation.html.twig', [
                'offre' => $donneesOffre,
                'devis' => $devis,
            ]);

        }

        return $this->render('envoi_devis/index.html.twig', [
            'form' => $form,
            'offre' => $donneesOffre,
            'montants' => $montants,
            'taux_tva' => AppConfig::TAUX_TVA
        ]);
    }

    #[Route('/contrat-de-maintenance', name: 'app_cdm')]
    public function contratDeMaintenance(): Response
    {
        return $this->render('envoi_devis/cdm.html.twig');
    }
    

}

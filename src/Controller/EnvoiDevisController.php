<?php

namespace App\Controller;

use App\Config\AppConfig;
use App\Entity\Devis;
use App\Form\DevisType;
use App\Service\EnvoiDevisService;
use App\Service\NotificationEmailService;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;

class EnvoiDevisController extends AbstractController
{
    #[Route('/devis/{offre}', name: 'app_devis')]
    public function devis(
        string $offre,
        Request $request,
        EnvoiDevisService $devisService,
        NotificationEmailService $notificationEmailService
    ): Response {
        $donneesOffre = $devisService->recupererDonneesOffre($offre);

        if (($donneesOffre['prix_site_ht'] ?? null) === null) {
            return $this->redirectToRoute('app_contact', [
                'sujet' => 'offre-pro',
            ]);
        }

        $optionsDevis = $devisService->recupererOptionsDevis();

        $montants = $devisService->calculerMontants(
            $donneesOffre,
            AppConfig::TAUX_TVA
        );

        $configurationRecapitulatif = $devisService->preparerConfigurationRecapitulatif(
            $donneesOffre,
            $optionsDevis,
            AppConfig::TAUX_TVA
        );

        $devis = new Devis();

        $devis->setContratMaintenance(false);
        $devis->setDomaine((bool) ($optionsDevis['nom_domaine']['active_par_defaut'] ?? false));
        $devis->setHebergement((bool) ($optionsDevis['hebergement']['active_par_defaut'] ?? false));

        $form = $this->createForm(DevisType::class, $devis);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            if ($devisService->enregistrerDevis($devis, $donneesOffre['libelle'])) {
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
            'optionsDevis' => $optionsDevis,
            'configurationRecapitulatif' => $configurationRecapitulatif,
            'taux_tva' => AppConfig::TAUX_TVA,
        ]);
    }

    #[Route('/webble-plus', name: 'app_cdm')]
    public function contratDeMaintenance(): Response
    {
        return $this->render('envoi_devis/cdm.html.twig', [
            'webblePlus' => AppConfig::WEBBLE_PLUS,
        ]);
    }
}

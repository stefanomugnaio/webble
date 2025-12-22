<?php

namespace App\Controller;

use App\Entity\Devis;
use App\Form\DevisType;
use App\Service\DevisService;
use App\Service\EnvoiDevisService;
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
        EnvoiDevisService $devisService
    ): Response
    {
        // 1. Données de l’offre (depuis pricing)
        $donneesOffre = $devisService->recupererDonneesOffre($offre);

        // 2. Montants
        $montants = $devisService->calculerMontants(
            $donneesOffre,
            0.20
        );

        // 3. Formulaire
        $devis = new Devis();
        $form = $this->createForm(DevisType::class, $devis);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {

            echo("isSubmit");
            $devisService->enregistrerDevis(
                $devis,
                $donneesOffre['libelle']
            );

            return $this->render('envoi_devis/confirmation.html.twig', [
                'offre' => $donneesOffre,
                'devis' => $devis,
            ]);

        }

        // 4. Rendu (template INCHANGÉ)
        return $this->render('envoi_devis/index.html.twig', [
            'form' => $form,
            'offre' => $donneesOffre,
            'montants' => $montants,
            'taux_tva' => 0.20,
        ]);
    }

    #[Route('/contrat-de-maintenance', name: 'app_cdm')]
    public function contratDeMaintenance(): Response
    {
        return $this->render('envoi_devis/cdm.html.twig');
    }

}

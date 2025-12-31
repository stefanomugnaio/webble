<?php

namespace App\Controller;

use App\Entity\Client;
use App\Form\RegistrationFormType;
use App\Service\RegistrationService;
use App\Repository\ClientRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Bundle\SecurityBundle\Security;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;

class RegistrationController extends AbstractController
{
    #[Route('/enregistrement', name: 'app_register')]
    public function register(
        Request $request,
        RegistrationService $clientService,
        ClientRepository $clientRepository,
        Security $security
    ): Response {

        // --------- CONTRÔLE D'ACCÈS SPÉCIAL ----------
        // S'il y a déjà des clients en base,
        // seul un ROLE_ADMIN peut accéder à cette page.
        $nbClients = $clientRepository->count([]);

        if ($nbClients > 0 && !$security->isGranted('ROLE_ADMIN')) {
            throw $this->createAccessDeniedException();
        }
        // ---------------------------------------------

        $client = new Client();

        // Création du formulaire
        $formulaire = $this->createForm(RegistrationFormType::class, $client);
        $formulaire->handleRequest($request);

        // Soumission + validation
        if ($formulaire->isSubmitted() && $formulaire->isValid()) {

            // Récupération du mot de passe texte
            $motDePasseClair = $formulaire->get('plainPassword')->getData();

            // Enregistrement via le service métier
            $clientService->enregistreClient($client, $motDePasseClair);

            // Redirection après inscription
            return $this->redirectToRoute('app_index');
        }

        // Affichage
        return $this->render('registration/register.html.twig', [
            'form' => $formulaire->createView(),
        ]);
    }
}

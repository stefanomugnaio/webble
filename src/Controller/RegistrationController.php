<?php

namespace App\Controller;

use App\Entity\Client;
use App\Form\RegistrationFormType;
use App\Service\RegistrationService;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Component\Security\Http\Attribute\IsGranted;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;

class RegistrationController extends AbstractController
{
    #[Route('/enregistrement', name: 'app_register')]
    #[IsGranted('ROLE_ADMIN')]
    public function register(
        Request $request,
        RegistrationService $clientService
    ): Response {
        
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

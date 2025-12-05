<?php

namespace App\Controller;

use DateTime;
use App\Entity\Contact;
use App\Form\ContactType;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;

final class ContactController extends AbstractController
{
    #[Route('/contact', name: 'app_contact')]
    public function index(Request $request, EntityManagerInterface $entityManager): Response
    {
        // Nouvel objet Contact
        $contact = new Contact();

        // Si tu veux initialiser la date ici (mais ce n’est pas obligatoire avant le submit)
        if (method_exists($contact, 'setDateCreation')) {
            $contact->setDateCreation(new DateTime());
        }

        // Création du formulaire
        $form = $this->createForm(ContactType::class, $contact);
        $form->handleRequest($request);

        // Soumission et validation
        if ($form->isSubmitted() && $form->isValid()) {
            // On force la date au moment de l’enregistrement
            if (method_exists($contact, 'setDateCreation')) {
                $contact->setDateCreation(new DateTime());
            }

            $entityManager->persist($contact);
            $entityManager->flush();

            // Message flash pour la vue
            $this->addFlash('success', 'Merci pour votre message, je vous réponds dès que possible.');

            // Redirection pour éviter la resoumission du formulaire
            return $this->redirectToRoute('app_contact');
        }

        // Affichage du formulaire
        return $this->render('contact/index.html.twig', [
            'form' => $form->createView(),
        ]);
    }
}

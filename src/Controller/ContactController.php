<?php

namespace App\Controller;

use App\Entity\Contact;
use App\Form\ContactType;
use App\Service\ContactService;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;

final class ContactController extends AbstractController
{
    #[Route('/contact', name: 'app_contact')]
    public function index(
        Request $request,
        ContactService $contactService
    ): Response {
        
        $contact = new Contact();

        $contactService->initialize($contact);

        // Création du formulaire
        $form = $this->createForm(ContactType::class, $contact);
        $form->handleRequest($request);

        // Soumission
        if ($form->isSubmitted() && $form->isValid()) {

            // Business : sauvegarde
            $contactService->save($contact);

            $this->addFlash('success', 'Merci pour votre message, je vous réponds dès que possible.');

            return $this->redirectToRoute('app_contact');
        }

        return $this->render('contact/index.html.twig', [
            'form' => $form->createView(),
        ]);
    }
}

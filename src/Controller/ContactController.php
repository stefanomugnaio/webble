<?php

namespace App\Controller;

use App\Entity\Contact;
use App\Form\ContactType;
use App\Service\ContactService;
use App\Service\NotificationEmailService;
use App\Service\RecaptchaService;
use Symfony\Component\Form\FormError;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;

final class ContactController extends AbstractController
{
    #[Route('/contact', name: 'app_contact')]
    public function index(
        Request $request,
        ContactService $contactService,
        NotificationEmailService $notificationEmailService,
        RecaptchaService $recaptchaService
    ): Response {

        $contact = new Contact();

        $contactService->initialize($contact);

        // Création du formulaire
        $form = $this->createForm(ContactType::class, $contact);
        $form->handleRequest($request);

        // Soumission
        if ($form->isSubmitted()) {

            // ===================== reCAPTCHA v3 =====================
            // Champ hidden non mappé : recaptchaToken
            $token = $form->get('recaptchaToken')->getData();

            $isHuman = $recaptchaService->verify($token, 'contact', 0.5);

            if (!$isHuman) {
                $form->addError(new FormError(
                    'La vérification anti-robot a échoué. Merci de réessayer.'
                ));
            }

            // ===================== Validation formulaire =====================
            if ($form->isValid()) {

                // Business : sauvegarde
                if ($contactService->save($contact)) {
                    $notificationEmailService->envoyerNotificationContact($contact->getDescription());
                }

                $this->addFlash('success', 'Merci pour votre message, je vous réponds dès que possible.');

                return $this->redirectToRoute('app_contact');
            }
        }

        return $this->render('contact/index.html.twig', [
            'form' => $form->createView(),
        ]);
    }
}

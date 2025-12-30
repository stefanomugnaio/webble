<?php

namespace App\Controller;

use App\Entity\Document;
use App\Form\ProfilClientType;
use App\Form\NotificationClientType;
use App\Repository\ContactRepository;
use App\Repository\DevisRepository;
use App\Service\DocumentService;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\BinaryFileResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\ResponseHeaderBag;
use Symfony\Component\Routing\Attribute\Route;

class ProfilController extends AbstractController
{
    #[Route('/profil', name: 'app_profil')]
    public function index(
        Request $request,
        EntityManagerInterface $em,
        DocumentService $documentService,
        ContactRepository $contactRepository,
        DevisRepository $devisRepository
    ): Response {
        $client = $this->getUser();

        if (!$client) {
            throw $this->createAccessDeniedException();
        }

        // --------------------------------------------------
        // Section affichée à droite
        // --------------------------------------------------
        $section = $request->query->get('section', 'profil');

        // --------------------------------------------------
        // FORMULAIRE PROFIL
        // --------------------------------------------------
        $formProfil = $this->createForm(ProfilClientType::class, $client);
        $formProfil->handleRequest($request);

        if ($section === 'profil' && $formProfil->isSubmitted() && $formProfil->isValid()) {
            $em->flush();

            $this->addFlash('success', 'Profil mis à jour.');
            return $this->redirectToRoute('app_profil', ['section' => 'profil']);
        }

        // --------------------------------------------------
        // FORMULAIRE NOTIFICATIONS
        // --------------------------------------------------
        $formNotifications = $this->createForm(NotificationClientType::class, $client);
        $formNotifications->handleRequest($request);

        if (
            $section === 'notifications'
            && $formNotifications->isSubmitted()
            && $formNotifications->isValid()
        ) {
            $em->flush();
            // $this->addFlash('success', 'Préférences de notification mises à jour.');
            // return $this->redirectToRoute('app_profil', ['section' => 'notifications']);
        }

        // --------------------------------------------------
        // DOCUMENTS (lecture seule)
        // --------------------------------------------------
        $documents = [];
        if ($section === 'documents') {
            $documents = $documentService->recupereDocumentsDuClient($client);
        }

        // --------------------------------------------------
        // MESSAGES = TABLE CONTACT (admin uniquement)
        // --------------------------------------------------
        $contacts = [];
        if ($section === 'messages') {
            $this->denyAccessUnlessGranted('ROLE_ADMIN');

            // On trie par date de création décroissante
            $contacts = $contactRepository->findBy([], [
                'date_creation' => 'DESC',
            ]);
        }

        // --------------------------------------------------
        // DEVIS = TABLE DEVIS (admin uniquement)
        // --------------------------------------------------
        $devisList = [];
        if ($section === 'devis') {
            $this->denyAccessUnlessGranted('ROLE_ADMIN');

            // Tous les devis, les plus récents en premier (par id)
            $devisList = $devisRepository->findBy([], [
                'id' => 'DESC',
            ]);
        }

        // --------------------------------------------------
        // RENDER
        // --------------------------------------------------
        return $this->render('profil/index.html.twig', [
            'client'            => $client,
            'section'           => $section,

            // formulaires
            'form'              => $formProfil->createView(),
            'formNotifications' => $formNotifications->createView(),

            // documents
            'documents'         => $documents,

            // messages (Contact)
            'contacts'          => $contacts,

            // devis (Devis)
            'devisList'         => $devisList,
        ]);
    }

    // --------------------------------------------------
    // TÉLÉCHARGEMENT DOCUMENT
    // --------------------------------------------------
    #[Route('/profil/document/{id}/telecharger', name: 'profil_document_telecharger')]
    public function telechargerDocument(Document $document): BinaryFileResponse
    {
        $client = $this->getUser();

        if (!$client) {
            throw $this->createAccessDeniedException();
        }

        // Sécurité : le document doit appartenir au client
        if ($document->getClient() !== $client) {
            throw $this->createAccessDeniedException();
        }

        $cheminFichier = $this->getParameter('kernel.project_dir')
            . '/public/'
            . $document->getChemin();

        if (!file_exists($cheminFichier)) {
            throw $this->createNotFoundException('Fichier introuvable');
        }

        $response = new BinaryFileResponse($cheminFichier);
        $response->setContentDisposition(
            ResponseHeaderBag::DISPOSITION_ATTACHMENT,
            $document->getNomFichier()
        );

        return $response;
    }
}

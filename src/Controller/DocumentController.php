<?php

namespace App\Controller;

use App\Form\DocumentType;
use App\Service\DocumentService;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;

class DocumentController extends AbstractController
{
    #[Route('/client/document/upload', name: 'client_document_upload')]
    public function upload(
        Request $request,
        DocumentService $documentService
    ): Response {
        $client = $this->getUser();

        if (!$client) {
            throw $this->createAccessDeniedException();
        }

        $form = $this->createForm(DocumentType::class);
        $form->handleRequest($request);

        $fichier = $form->get('fichier')->getData();
        dd($fichier);
        if ($form->isSubmitted() && $form->isValid()) {

            $documentService->enregistreDocumentPourClient($fichier,$client);

            $this->addFlash('success', 'Document ajouté avec succès.');

            return $this->redirectToRoute('app_profil');
        }

        return $this->render('document/upload.html.twig', [
            'form' => $form->createView(),
        ]);
    }
}

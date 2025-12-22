<?php

namespace App\Controller;

use App\Entity\Client;
use App\Form\DocumentUploadType;
use App\Service\DocumentUploadService;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;

class UploadDocumentController extends AbstractController
{
    #[Route('/upload', name: 'admin_document_upload')]
    public function upload(
        Request $request,
        EntityManagerInterface $em,
        DocumentUploadService $documentUploadService
    ): Response {
        // 🔐 sécurité admin
        $this->denyAccessUnlessGranted('ROLE_ADMIN');

        $clients = $em->getRepository(Client::class)->findAll();

        $form = $this->createForm(DocumentUploadType::class, null, [
            'clients' => $clients,
        ]);

        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {

            $client = $form->get('client')->getData();
            $nom = $form->get('nom')->getData();
            $fichier = $form->get('fichier')->getData();

            $documentUploadService->upload($client, $nom, $fichier);

            $this->addFlash('success', 'Document uploadé avec succès.');

            return $this->redirectToRoute('admin_document_upload');
        }

        return $this->render('upload_document/index.html.twig', [
            'form' => $form->createView(),
        ]);
    }
}

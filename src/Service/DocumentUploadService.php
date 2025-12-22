<?php

namespace App\Service;

use App\Entity\Client;
use App\Entity\Document;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\HttpFoundation\File\UploadedFile;

class DocumentUploadService
{
    public function __construct(
        private EntityManagerInterface $em,
        private string $documentsDir,
        private NotificationEmailService $notificationEmailService
    ) {}

    public function upload(
        Client $client,
        string $nom,
        UploadedFile $fichier
    ): void {
        $nomFichier = uniqid().'_'.$fichier->getClientOriginalName();

        $fichier->move($this->documentsDir, $nomFichier);

        $document = new Document();
        $document->setNom($nom);
        $document->setNomFichier($nomFichier);
        $document->setChemin('uploads/documents/'.$nomFichier);
        $document->setClient($client);

        $this->em->persist($document);
        $this->em->flush();

        // 🔔 NOTIFICATION EMAIL (si activée)
        if ($client->isNotificationDocument()) {
            $this->notificationEmailService
                ->envoyerNotificationDocument($client, $document);
        }
    }
}


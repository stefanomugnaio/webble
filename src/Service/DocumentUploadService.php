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
        // 🔹 Nom du dossier client (nettoyé)
        $nomClient = preg_replace('/[^a-zA-Z0-9_-]/', '', $client->getNom());

        // 🔹 Dossier cible du client
        $dossierClient = $this->documentsDir . '/' . $nomClient;

        // 🔹 Création du dossier s’il n’existe pas
        if (!is_dir($dossierClient)) {
            mkdir($dossierClient, 0755, true);
        }

        // 🔹 Nom du fichier
        $nomFichier = uniqid() . '_' . $fichier->getClientOriginalName();

        // 🔹 Déplacement du fichier dans le dossier du client
        $fichier->move($dossierClient, $nomFichier);

        // 🔹 Enregistrement en base
        $document = new Document();
        $document->setNom($nom);
        $document->setNomFichier($nomFichier);
        $document->setChemin('uploads/documents/' . $nomClient . '/' . $nomFichier);
        $document->setClient($client);

        $this->em->persist($document);
        $this->em->flush();

        // 🔔 Notification email
        if ($client->isNotificationDocument()) {
            $this->notificationEmailService
                ->envoyerNotificationDocument($client, $document);
        }
    }

}


<?php

namespace App\Service;

use App\Entity\Client;
use App\Entity\Document;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\DependencyInjection\ParameterBag\ParameterBagInterface;
use Symfony\Component\HttpFoundation\File\UploadedFile;

class DocumentService
{
    public function __construct(
        private EntityManagerInterface $em,
        private ParameterBagInterface $params
    ) {}

    /**
     * Enregistre un document PDF pour un client.
     */
    public function enregistreDocumentPourClient(
        UploadedFile $fichier,
        Client $client,
        string $nomDocument
    ): Document {

        // Nom du fichier stocké
        $nomFichier = uniqid('doc_', true).'.'.$fichier->guessExtension();

        // Dossier client
        $dossierClient = $this->params->get('documents_directory')
            .'/client_'.$client->getId();

        if (!is_dir($dossierClient)) {
            mkdir($dossierClient, 0775, true);
        }

        // Déplacement du fichier
        $fichier->move($dossierClient, $nomFichier);

        // Chemin relatif pour Twig
        $cheminRelatif = 'uploads/documents/client_'.$client->getId().'/'.$nomFichier;

        // Entité Document (STRICTEMENT selon ton modèle)
        $document = new Document();
        $document->setNom($nomDocument);
        $document->setNomFichier($nomFichier);
        $document->setChemin($cheminRelatif);
        $document->setClient($client);

        $this->em->persist($document);
        $this->em->flush();

        return $document;
    }

    /**
     * Récupère les documents d’un client.
     */
    public function recupereDocumentsDuClient(Client $client): array
    {
        return $this->em->getRepository(Document::class)->findBy(
            ['client' => $client],
            ['id' => 'DESC']
        );
    }
}

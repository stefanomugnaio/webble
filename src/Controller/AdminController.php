<?php

namespace App\Controller;

use App\Entity\Client;
use App\Entity\Contact;
use App\Entity\Devis;
use App\Entity\DevisFormation;
use App\Entity\DisponibiliteFormation;
use App\Entity\Document;
use App\Entity\Formation;
use App\Entity\OffreCommerciale;
use App\Entity\TacheOffre;
use App\Form\AdminClientType;
use App\Form\AdminContactType;
use App\Form\AdminDevisFormationType;
use App\Form\AdminDevisType;
use App\Form\AdminDocumentType;
use App\Form\AdminFormationType;
use App\Form\DocumentUploadType;
use App\Service\DocumentUploadService;
use Doctrine\DBAL\Exception\ForeignKeyConstraintViolationException;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Form\FormError;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Component\Security\Http\Attribute\IsGranted;

#[Route('/admin', name: 'admin_')]
#[IsGranted('ROLE_ADMIN')]
class AdminController extends AbstractController
{
    #[Route('', name: 'dashboard', methods: ['GET'])]
    public function dashboard(EntityManagerInterface $em): Response
    {
        return $this->render('admin/dashboard.html.twig', [
            'counts' => [
                'contacts' => $em->getRepository(Contact::class)->count([]),
                'devis' => $em->getRepository(Devis::class)->count([]),
                'devis_formations' => $em->getRepository(DevisFormation::class)->count([]),
                'disponibilites' => $em->getRepository(DisponibiliteFormation::class)->count([]),
                'formations' => $em->getRepository(Formation::class)->count([]),
                'clients' => $em->getRepository(Client::class)->count([]),
                'documents' => $em->getRepository(Document::class)->count([]),
                'offres_commerciales' => $em->getRepository(OffreCommerciale::class)->count([]),
                'taches_offres' => $em->getRepository(TacheOffre::class)->count([]),
            ],
        ]);
    }

    #[Route('/contacts', name: 'contact_index', methods: ['GET'])]
    public function contacts(EntityManagerInterface $em): Response
    {
        $items = array_map(fn (Contact $item) => [
            'id' => $item->getId(),
            'values' => [
                $item->getDateCreation()?->format('d/m/Y') ?? '—',
                $item->getPrenom().' '.$item->getNom(),
                $item->getEmail(),
                $item->getSujet(),
            ],
        ], $em->getRepository(Contact::class)->findBy([], ['date_creation' => 'DESC']));

        return $this->list('contact', 'Messages de contact', 'Tous les messages reçus depuis le formulaire public.',
            ['Date', 'Contact', 'E-mail', 'Sujet'], $items);
    }

    #[Route('/contacts/nouveau', name: 'contact_new', methods: ['GET', 'POST'])]
    #[Route('/contacts/{id}/modifier', name: 'contact_edit', methods: ['GET', 'POST'], requirements: ['id' => '\\d+'])]
    public function contactForm(Request $request, EntityManagerInterface $em, ?Contact $contact = null): Response
    {
        $contact ??= (new Contact())->setDateCreation(new \DateTime());
        $isNew = null === $contact->getId();
        $form = $this->createForm(AdminContactType::class, $contact)->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $em->persist($contact);
            $em->flush();
            $this->addFlash('success', $isNew ? 'Message créé.' : 'Message mis à jour.');
            return $this->redirectToRoute('admin_contact_index');
        }

        return $this->form($form, $isNew ? 'Créer un message' : 'Modifier le message', 'contact');
    }

    #[Route('/contacts/{id}', name: 'contact_show', methods: ['GET'], requirements: ['id' => '\\d+'])]
    public function contactShow(Contact $contact): Response
    {
        return $this->show('contact', 'Message de '.$contact->getPrenom().' '.$contact->getNom(), $contact->getId(), [
            'Reçu le' => $contact->getDateCreation()?->format('d/m/Y') ?? '—',
            'Nom' => $contact->getPrenom().' '.$contact->getNom(),
            'E-mail' => $contact->getEmail(),
            'Sujet' => $contact->getSujet(),
            'Numéro de contrat' => $contact->getNumContrat() ?: '—',
            'Message' => $contact->getDescription() ?: '—',
        ]);
    }

    #[Route('/contacts/{id}', name: 'contact_delete', methods: ['POST'], requirements: ['id' => '\\d+'])]
    public function contactDelete(Contact $contact, Request $request, EntityManagerInterface $em): Response
    {
        $this->remove($contact, $request, $em, 'contact');
        return $this->redirectToRoute('admin_contact_index');
    }

    #[Route('/devis', name: 'devis_index', methods: ['GET'])]
    public function devis(EntityManagerInterface $em): Response
    {
        $items = array_map(fn (Devis $item) => [
            'id' => $item->getId(),
            'values' => [$item->getPrenom().' '.$item->getNom(), $item->getEmail(), $item->getOffre(), $item->getVille()],
        ], $em->getRepository(Devis::class)->findBy([], ['id' => 'DESC']));

        return $this->list('devis', 'Demandes de devis', 'Demandes concernant la création de sites Webble.',
            ['Contact', 'E-mail', 'Offre', 'Ville'], $items);
    }

    #[Route('/devis/nouveau', name: 'devis_new', methods: ['GET', 'POST'])]
    #[Route('/devis/{id}/modifier', name: 'devis_edit', methods: ['GET', 'POST'], requirements: ['id' => '\\d+'])]
    public function devisForm(Request $request, EntityManagerInterface $em, ?Devis $devis = null): Response
    {
        $devis ??= new Devis();
        $isNew = null === $devis->getId();
        $form = $this->createForm(AdminDevisType::class, $devis)->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $em->persist($devis);
            $em->flush();
            $this->addFlash('success', $isNew ? 'Devis créé.' : 'Devis mis à jour.');
            return $this->redirectToRoute('admin_devis_index');
        }

        return $this->form($form, $isNew ? 'Créer un devis' : 'Modifier le devis', 'devis');
    }

    #[Route('/devis/{id}', name: 'devis_show', methods: ['GET'], requirements: ['id' => '\\d+'])]
    public function devisShow(Devis $devis): Response
    {
        return $this->show('devis', 'Devis de '.$devis->getPrenom().' '.$devis->getNom(), $devis->getId(), [
            'E-mail' => $devis->getEmail(), 'Téléphone' => $devis->getTelephone(),
            'Adresse' => $devis->getAdresse().', '.$devis->getCodePostal().' '.$devis->getVille(),
            'Offre' => $devis->getOffre(), 'Description' => $devis->getDescription() ?: '—',
            'Webble+' => $this->yesNo($devis->isContratMaintenance()), 'Nom de domaine' => $this->yesNo($devis->isDomaine()),
            'Hébergement' => $this->yesNo($devis->isHebergement()), 'RGPD' => $this->yesNo($devis->isRgpd()),
            'CGV' => $this->yesNo($devis->isCgv()),
        ]);
    }

    #[Route('/devis/{id}', name: 'devis_delete', methods: ['POST'], requirements: ['id' => '\\d+'])]
    public function devisDelete(Devis $devis, Request $request, EntityManagerInterface $em): Response
    {
        $this->remove($devis, $request, $em, 'devis');
        return $this->redirectToRoute('admin_devis_index');
    }

    #[Route('/demandes-formation', name: 'devis_formation_index', methods: ['GET'])]
    public function devisFormations(EntityManagerInterface $em): Response
    {
        $items = array_map(fn (DevisFormation $item) => [
            'id' => $item->getId(),
            'values' => [
                $item->getDateDemande()?->format('d/m/Y') ?? '—',
                $item->getPrenom().' '.$item->getNom(),
                $item->getFormations()?->getLibelle() ?? '—',
                $item->getDateSouhaitee()?->format('d/m/Y') ?? 'Ancienne demande',
            ],
        ], $em->getRepository(DevisFormation::class)->findBy([], ['id' => 'DESC']));

        return $this->list('devis_formation', 'Demandes de formation', 'Journées disponibles choisies par les utilisateurs.',
            ['Demande', 'Contact', 'Formation', 'Journée choisie'], $items);
    }

    #[Route('/demandes-formation/nouvelle', name: 'devis_formation_new', methods: ['GET', 'POST'])]
    #[Route('/demandes-formation/{id}/modifier', name: 'devis_formation_edit', methods: ['GET', 'POST'], requirements: ['id' => '\\d+'])]
    public function devisFormationForm(Request $request, EntityManagerInterface $em, ?DevisFormation $devis = null): Response
    {
        $devis ??= (new DevisFormation())->setDateDemande(new \DateTime())->setRgpd(false);
        $isNew = null === $devis->getId();
        $form = $this->createForm(AdminDevisFormationType::class, $devis)->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $em->persist($devis);
            $em->flush();
            $this->addFlash('success', $isNew ? 'Demande créée.' : 'Demande mise à jour.');
            return $this->redirectToRoute('admin_devis_formation_index');
        }

        return $this->form($form, $isNew ? 'Créer une demande de formation' : 'Modifier la demande', 'devis_formation');
    }

    #[Route('/demandes-formation/{id}', name: 'devis_formation_show', methods: ['GET'], requirements: ['id' => '\\d+'])]
    public function devisFormationShow(DevisFormation $devis): Response
    {
        $legacy = $devis->getSessionFormation();
        $creneau = $devis->getDateSouhaitee()?->format('d/m/Y');
        if (!$creneau && $legacy) {
            $creneau = 'Ancienne session du '.$legacy->getDateDebut()?->format('d/m/Y').' au '.$legacy->getDateFin()?->format('d/m/Y');
        }

        return $this->show('devis_formation', 'Demande de '.$devis->getPrenom().' '.$devis->getNom(), $devis->getId(), [
            'Reçue le' => $devis->getDateDemande()?->format('d/m/Y') ?? '—', 'E-mail' => $devis->getEmail(),
            'Téléphone' => $devis->getTelephone(), 'Adresse' => $devis->getAdresse().', '.$devis->getCodePostal().' '.$devis->getVille(),
            'Formation' => $devis->getFormations()?->getLibelle() ?? '—', 'Journée choisie' => $creneau ?: 'À définir',
            'RGPD' => $this->yesNo($devis->isRgpd()),
        ]);
    }

    #[Route('/demandes-formation/{id}', name: 'devis_formation_delete', methods: ['POST'], requirements: ['id' => '\\d+'])]
    public function devisFormationDelete(DevisFormation $devis, Request $request, EntityManagerInterface $em): Response
    {
        $this->remove($devis, $request, $em, 'devis_formation');
        return $this->redirectToRoute('admin_devis_formation_index');
    }

    #[Route('/formations', name: 'formation_index', methods: ['GET'])]
    public function formations(EntityManagerInterface $em): Response
    {
        $items = array_map(fn (Formation $item) => [
            'id' => $item->getId(),
            'values' => [$item->getLibelle(), $item->getSlug(), number_format((float) $item->getPrix(), 2, ',', ' ').' €', $item->getDuree().' h'],
        ], $em->getRepository(Formation::class)->findBy([], ['libelle' => 'ASC']));

        return $this->list('formation', 'Formations', 'Catalogue des formations proposées sur le site.',
            ['Formation', 'URL', 'Prix HT', 'Durée'], $items);
    }

    #[Route('/formations/nouvelle', name: 'formation_new', methods: ['GET', 'POST'])]
    #[Route('/formations/{id}/modifier', name: 'formation_edit', methods: ['GET', 'POST'], requirements: ['id' => '\\d+'])]
    public function formationForm(Request $request, EntityManagerInterface $em, ?Formation $formation = null): Response
    {
        $formation ??= new Formation();
        $isNew = null === $formation->getId();
        $form = $this->createForm(AdminFormationType::class, $formation)->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $slug = $this->slugify((string) ($formation->getSlug() ?: $formation->getLibelle()));
            $existing = $em->getRepository(Formation::class)->findOneBy(['slug' => $slug]);

            if ($existing && $existing !== $formation) {
                $form->get('slug')->addError(new FormError('Cet identifiant URL est déjà utilisé.'));
            } else {
                $formation->setSlug($slug);
                $em->persist($formation);
                $em->flush();
                $this->addFlash('success', $isNew ? 'Formation créée.' : 'Formation mise à jour.');
                return $this->redirectToRoute('admin_formation_index');
            }
        }

        return $this->form($form, $isNew ? 'Créer une formation' : 'Modifier la formation', 'formation');
    }

    #[Route('/formations/{id}', name: 'formation_show', methods: ['GET'], requirements: ['id' => '\\d+'])]
    public function formationShow(Formation $formation): Response
    {
        return $this->show('formation', $formation->getLibelle(), $formation->getId(), [
            'Identifiant URL' => $formation->getSlug(), 'Prix HT' => number_format((float) $formation->getPrix(), 2, ',', ' ').' €',
            'Durée' => $formation->getDuree().' heures', 'Description' => $formation->getDescription(),
            'Demandes associées' => (string) $formation->getDevisFormation()->count(),
        ]);
    }

    #[Route('/formations/{id}', name: 'formation_delete', methods: ['POST'], requirements: ['id' => '\\d+'])]
    public function formationDelete(Formation $formation, Request $request, EntityManagerInterface $em): Response
    {
        if ($this->isCsrfTokenValid('delete_formation'.$formation->getId(), $request->request->getString('_token'))) {
            try {
                $em->remove($formation);
                $em->flush();
                $this->addFlash('success', 'Formation supprimée.');
            } catch (ForeignKeyConstraintViolationException) {
                $this->addFlash('danger', 'Cette formation possède encore des demandes ou des sessions associées.');
            }
        }
        return $this->redirectToRoute('admin_formation_index');
    }

    #[Route('/clients', name: 'client_index', methods: ['GET'])]
    public function clients(EntityManagerInterface $em): Response
    {
        $items = array_map(fn (Client $item) => [
            'id' => $item->getId(),
            'values' => [
                $item->getPrenom().' '.$item->getNom(), $item->getEmail(), $item->getTelephone() ?: '—',
                in_array('ROLE_ADMIN', $item->getRoles(), true) ? 'Administrateur' : 'Client',
            ],
        ], $em->getRepository(Client::class)->findBy([], ['nom' => 'ASC']));

        return $this->list('client', 'Clients', 'Comptes disposant d’un accès à l’espace client.',
            ['Client', 'E-mail', 'Téléphone', 'Rôle'], $items);
    }

    #[Route('/clients/nouveau', name: 'client_new', methods: ['GET', 'POST'])]
    #[Route('/clients/{id}/modifier', name: 'client_edit', methods: ['GET', 'POST'], requirements: ['id' => '\\d+'])]
    public function clientForm(Request $request, EntityManagerInterface $em, UserPasswordHasherInterface $hasher, ?Client $client = null): Response
    {
        $client ??= (new Client())->setNotificationDocument(false);
        $isNew = null === $client->getId();
        $form = $this->createForm(AdminClientType::class, $client, ['password_required' => $isNew])->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $plainPassword = $form->get('plainPassword')->getData();
            if ($plainPassword) {
                $client->setPassword($hasher->hashPassword($client, $plainPassword));
            }
            $em->persist($client);
            $em->flush();
            $this->addFlash('success', $isNew ? 'Client créé.' : 'Client mis à jour.');
            return $this->redirectToRoute('admin_client_index');
        }

        return $this->form($form, $isNew ? 'Créer un client' : 'Modifier le client', 'client');
    }

    #[Route('/clients/{id}', name: 'client_show', methods: ['GET'], requirements: ['id' => '\\d+'])]
    public function clientShow(Client $client): Response
    {
        return $this->show('client', $client->getPrenom().' '.$client->getNom(), $client->getId(), [
            'E-mail' => $client->getEmail(), 'Téléphone' => $client->getTelephone() ?: '—', 'Pays' => $client->getPays(),
            'Rôles' => implode(', ', $client->getRoles()), 'Notifications documents' => $this->yesNo($client->isNotificationDocument()),
            'Documents' => (string) $client->getDocuments()->count(),
        ]);
    }

    #[Route('/clients/{id}', name: 'client_delete', methods: ['POST'], requirements: ['id' => '\\d+'])]
    public function clientDelete(Client $client, Request $request, EntityManagerInterface $em): Response
    {
        if ($client === $this->getUser()) {
            $this->addFlash('danger', 'Vous ne pouvez pas supprimer votre propre compte.');
            return $this->redirectToRoute('admin_client_index');
        }

        if ($this->isCsrfTokenValid('delete_client'.$client->getId(), $request->request->getString('_token'))) {
            try {
                $em->remove($client);
                $em->flush();
                $this->addFlash('success', 'Client supprimé.');
            } catch (ForeignKeyConstraintViolationException) {
                $this->addFlash('danger', 'Ce client possède encore des documents associés.');
            }
        }
        return $this->redirectToRoute('admin_client_index');
    }

    #[Route('/documents', name: 'document_index', methods: ['GET'])]
    public function documents(EntityManagerInterface $em): Response
    {
        $items = array_map(fn (Document $item) => [
            'id' => $item->getId(),
            'values' => [
                $item->getNom(), $item->getNomFichier(),
                $item->getClient() ? $item->getClient()->getPrenom().' '.$item->getClient()->getNom() : 'Sans client',
            ],
        ], $em->getRepository(Document::class)->findBy([], ['id' => 'DESC']));

        return $this->list('document', 'Documents', 'Documents déposés dans les espaces clients.',
            ['Document', 'Fichier', 'Client'], $items);
    }

    #[Route('/documents/nouveau', name: 'document_new', methods: ['GET', 'POST'])]
    public function documentNew(Request $request, EntityManagerInterface $em, DocumentUploadService $uploader): Response
    {
        $form = $this->createForm(DocumentUploadType::class, null, [
            'clients' => $em->getRepository(Client::class)->findBy([], ['nom' => 'ASC']),
        ])->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $uploader->upload($form->get('client')->getData(), $form->get('nom')->getData(), $form->get('fichier')->getData());
            $this->addFlash('success', 'Document ajouté.');
            return $this->redirectToRoute('admin_document_index');
        }

        return $this->form($form, 'Ajouter un document', 'document');
    }

    #[Route('/documents/{id}/modifier', name: 'document_edit', methods: ['GET', 'POST'], requirements: ['id' => '\\d+'])]
    public function documentEdit(Document $document, Request $request, EntityManagerInterface $em): Response
    {
        $form = $this->createForm(AdminDocumentType::class, $document)->handleRequest($request);
        if ($form->isSubmitted() && $form->isValid()) {
            $em->flush();
            $this->addFlash('success', 'Document mis à jour.');
            return $this->redirectToRoute('admin_document_index');
        }
        return $this->form($form, 'Modifier le document', 'document');
    }

    #[Route('/documents/{id}', name: 'document_show', methods: ['GET'], requirements: ['id' => '\\d+'])]
    public function documentShow(Document $document): Response
    {
        return $this->show('document', $document->getNom(), $document->getId(), [
            'Nom du fichier' => $document->getNomFichier(), 'Chemin' => $document->getChemin(),
            'Client' => $document->getClient() ? $document->getClient()->getPrenom().' '.$document->getClient()->getNom() : 'Sans client',
        ]);
    }

    #[Route('/documents/{id}', name: 'document_delete', methods: ['POST'], requirements: ['id' => '\\d+'])]
    public function documentDelete(Document $document, Request $request, EntityManagerInterface $em): Response
    {
        if ($this->isCsrfTokenValid('delete_document'.$document->getId(), $request->request->getString('_token'))) {
            $publicUploads = realpath($this->getParameter('kernel.project_dir').'/public/uploads/documents');
            $file = realpath($this->getParameter('kernel.project_dir').'/public/'.ltrim($document->getChemin(), '/\\'));

            if ($file && $publicUploads && str_starts_with(strtolower($file), strtolower($publicUploads.DIRECTORY_SEPARATOR))) {
                unlink($file);
            }

            $em->remove($document);
            $em->flush();
            $this->addFlash('success', 'Document supprimé.');
        }
        return $this->redirectToRoute('admin_document_index');
    }

    private function list(string $resource, string $title, string $description, array $columns, array $items): Response
    {
        return $this->render('admin/list.html.twig', compact('resource', 'title', 'description', 'columns', 'items'));
    }

    private function form(mixed $form, string $title, string $resource): Response
    {
        return $this->render('admin/form.html.twig', ['form' => $form, 'title' => $title, 'resource' => $resource]);
    }

    private function show(string $resource, string $title, int $id, array $fields): Response
    {
        return $this->render('admin/show.html.twig', compact('resource', 'title', 'id', 'fields'));
    }

    private function remove(object $entity, Request $request, EntityManagerInterface $em, string $resource): void
    {
        if ($this->isCsrfTokenValid('delete_'.$resource.$entity->getId(), $request->request->getString('_token'))) {
            $em->remove($entity);
            $em->flush();
            $this->addFlash('success', 'Élément supprimé.');
        }
    }

    private function yesNo(?bool $value): string
    {
        return $value ? 'Oui' : 'Non';
    }

    private function slugify(string $value): string
    {
        $ascii = iconv('UTF-8', 'ASCII//TRANSLIT//IGNORE', $value) ?: $value;
        return trim(strtolower((string) preg_replace('/[^a-zA-Z0-9]+/', '-', $ascii)), '-');
    }
}

<?php

namespace App\Service;

use App\Entity\Client;
use App\Entity\Devis;
use App\Entity\DevisFormation;
use App\Entity\Document;
use Symfony\Component\Mailer\Exception\TransportExceptionInterface;
use Symfony\Component\Mailer\MailerInterface;
use Symfony\Component\Mime\Email;

class NotificationEmailService
{
    public function __construct(
        private MailerInterface $mailer,
        private string $fromEmail
    ) {}

    public function envoyerNotificationDocument(Client $client,Document $document): void {
        $email = (new Email())
            ->from('postmaster@webble.fr')
            ->to($client->getEmail())
            ->subject('Nouveau document disponible')
            ->html("
                <p>Bonjour {$client->getPrenom()},</p>

                <p>Un nouveau document est disponible dans votre espace client :</p>

                <p><strong>{$document->getNom()}</strong></p>

                <p>
                    Connectez-vous à votre espace client pour le consulter.
                </p>

                <p>
                    — L’équipe
                </p>
            ");

        try {
            $this->mailer->send($email);
        } catch (TransportExceptionInterface $e) {
            dd($e->getMessage());
        }
    }

    public function envoyerNotificationContact($description): void {
        $email = (new Email())
            ->from('postmaster@webble.fr')
            ->to('stephane.mn@outlook.fr')
            ->subject('Un nouveau message est arrivé')
            ->html($description);

        try {
            $this->mailer->send($email);
        } catch (TransportExceptionInterface $e) {
            dd($e->getMessage());
        }
    }

    public function envoyerNotificationDevis(Devis $devis): void {
        $emailVersWebble = (new Email())
            ->from('postmaster@webble.fr')
            ->to('stephane.mn@outlook.fr')
            ->subject('Un nouvelle demande de devis vient d\'arriver !')
            ->html("
                <p>Descriptif de la demande :</p>

                <h3><strong>Informations personnelles :</h3></strong>
                <p>Email : <strong>{$devis->getEmail()}</strong></p>
                <p>Nom : <strong>{$devis->getNom()}</strong></p>
                <p>Prénom : <strong>{$devis->getPrenom()}</strong></p>
                <p>Tél : <strong>{$devis->getTelephone()}</strong></p>
                <p>Ville : <strong>{$devis->getVille()}</strong></p>
                <p>Code postal : <strong>{$devis->getCodePostal()}</strong></p>
                <p>Adresse : <strong>{$devis->getAdresse()}</strong></p>
                <p>Offre choisie : <strong>{$devis->getDescription()}</strong></p>
                <p>Offre choisie : <strong>{$devis->getOffre()}</strong></p>

                <h3> Options : </h3>
                <p>Contrat de maintenance : <strong>{$devis->isContratMaintenance()}</strong></p>
                <p>Domaine : <strong>{$devis->isDomaine()}</strong></p>
                <p>Hébergement : <strong>{$devis->isHebergement()}</strong></p>
            ");

        try {
            $this->mailer->send($emailVersWebble);
        } catch (TransportExceptionInterface $e) {
            dd($e->getMessage());
        }

        $emailVersClient = (new Email())
            ->from('postmaster@webble.fr')
            ->to($devis->getEmail())
            ->subject('Confirmation de réception de votre demande de devis')
            ->html("
                <p>Bonjour,</p>

                <p>
                    Votre demande a bien été reçue et est actuellement en cours d’analyse.
                </p>

                <p>
                    Vous recevrez un retour personnalisé dans les plus brefs délais.
                </p>

                <p>
                    Si vous avez des informations complémentaires à me transmettre d’ici là,
                    n’hésitez pas à m'envoyer un message via le formulaire de contact.
                </p>

                <p>
                    Bien cordialement,<br>
                    <strong>Stéfano Maniero</strong><br>
                    Webble
                </p>
            ");

        try {
            $this->mailer->send($emailVersClient);
        } catch (TransportExceptionInterface $e) {
            dd($e->getMessage());
        }
    }

    public function envoyerNotificationFormation(DevisFormation $devisFormation): void {
        
        $sessionFormation = $devisFormation->getSessionFormation();
        $formation = $devisFormation->getFormations();
        $emailVersWebble = (new Email())
            ->from('postmaster@webble.fr')
            ->to('stephane.mn@outlook.fr')
            ->subject('Un nouvelle demande de formation vient d\'arriver !')
            ->html("
                <p>Descriptif de la demande :</p>

                <h3><strong>Informations personnelles :</h3></strong>
                <p>Email : <strong>{$devisFormation->getEmail()}</strong></p>
                <p>Nom : <strong>{$devisFormation->getNom()}</strong></p>
                <p>Prénom : <strong>{$devisFormation->getPrenom()}</strong></p>
                <p>Tél : <strong>{$devisFormation->getTelephone()}</strong></p>
                <p>Ville : <strong>{$devisFormation->getVille()}</strong></p>
                <p>Code postal : <strong>{$devisFormation->getCodePostal()}</strong></p>
                <p>Adresse : <strong>{$devisFormation->getAdresse()}</strong></p>
                
                <h3> Détail de la formation : </h3>
                <p>Session choisie : <strong>Du {$sessionFormation->getDateDebut()->format('d/m/Y')} au {$sessionFormation->getDateFin()->format('d/m/Y')}</strong></p>
                <p>Formation : <strong>{$formation->getLibelle()}</strong></p>
                <p>Durée : <strong>{$formation->getDuree()}</strong></p>

            ");

        try {
            $this->mailer->send($emailVersWebble);
        } catch (TransportExceptionInterface $e) {
            dd($e->getMessage());
        }

        $emailVersClient = (new Email())
            ->from('postmaster@webble.fr')
            ->to($devisFormation->getEmail())
            ->subject('Confirmation de réception de votre demande de formation')
            ->html("
                <p>Bonjour,</p>

                <p>
                    Votre demande de formation a bien été reçue et est actuellement en cours d’analyse.
                </p>

                <p>
                    Une offre détaillée vous sera prochainement transmise à l’adresse email indiquée.
                </p>

                <p>
                    Après réception de celle-ci signée, je prendrai directement contact avec vous afin d’échanger
                    sur les détails et planifier la formation.
                </p>

                <p>
                    Si vous avez des informations complémentaires à me transmettre d’ici là,
                    n’hésitez pas à m'envoyer un message via le formulaire de contact.
                </p>
            ");

        try {
            $this->mailer->send($emailVersClient);
        } catch (TransportExceptionInterface $e) {
            dd($e->getMessage());
        }
    }
}

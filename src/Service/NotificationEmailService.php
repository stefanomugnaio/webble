<?php

namespace App\Service;

use App\Entity\Client;
use App\Entity\Document;
use Symfony\Component\Mailer\MailerInterface;
use Symfony\Component\Mime\Email;

class NotificationEmailService
{
    public function __construct(
        private MailerInterface $mailer,
        private string $fromEmail
    ) {}

    public function envoyerNotificationDocument(
        Client $client,
        Document $document
    ): void {
        $email = (new Email())
            ->from($this->fromEmail)
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

        $this->mailer->send($email);
    }
}

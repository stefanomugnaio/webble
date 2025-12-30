<?php

namespace App\Service;

use App\Entity\Client;
use App\Entity\Document;
use Symfony\Component\Mime\Email;
use Symfony\Component\Mailer\MailerInterface;
use Symfony\Component\Mailer\Exception\TransportExceptionInterface;

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

    public function envoyerNotificationDevis($description): void {
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
}

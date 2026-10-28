<?php

namespace App\Service;

use App\Config\AppConfig;
use App\Entity\Client;
use App\Entity\Devis;
use App\Entity\DevisFormation;
use App\Entity\Document;
use Psr\Log\LoggerInterface;
use Symfony\Component\Mailer\MailerInterface;
use Symfony\Component\Mime\Address;
use Symfony\Component\Mime\Email;
use Twig\Environment;

class NotificationEmailService
{
    public function __construct(
        private MailerInterface $mailer,
        private Environment $twig,
        private string $fromEmail,
        private array $notificationEmails,
        private LoggerInterface $logger
    ) {
        $this->notificationEmails = array_values(array_unique(array_filter(
            array_map('trim', $this->notificationEmails),
            static fn (string $email): bool => false !== filter_var($email, FILTER_VALIDATE_EMAIL)
        )));

        if ([] === $this->notificationEmails) {
            throw new \InvalidArgumentException('Au moins une adresse de notification valide doit être configurée.');
        }
    }

    public function envoyerNotificationDocument(Client $client, Document $document): void
    {
        $this->envoyerEmail(function () use ($client, $document): Email {
            return (new Email())
                ->from(new Address($this->fromEmail, 'Webble'))
                ->to($client->getEmail())
                ->subject('Un nouveau document est disponible — Webble')
                ->html(sprintf(
                    '<p>Bonjour %s,</p><p>Un nouveau document est disponible dans votre espace client :</p><p><strong>%s</strong></p><p>Bien cordialement,<br><strong>Webble</strong></p>',
                    htmlspecialchars((string) $client->getPrenom(), ENT_QUOTES),
                    htmlspecialchars((string) $document->getNom(), ENT_QUOTES)
                ));
        }, 'notification de document');
    }

    public function envoyerNotificationContact($description): void
    {
        $this->envoyerEmail(function () use ($description): Email {
            return (new Email())
                ->from(new Address($this->fromEmail, 'Webble'))
                ->to(...$this->notificationEmails)
                ->subject('[Webble] Nouveau message de contact')
                ->html((string) $description);
        }, 'notification de contact');
    }

    public function envoyerNotificationDevis(Devis $devis, array $recapitulatif): void
    {
        $contexte = [
            'devis' => $devis,
            'recapitulatif' => $recapitulatif,
            'logo_url' => 'https://webble.fr/images/webble_black.png',
            'contact_url' => 'https://webble.fr/contact',
        ];

        $this->envoyerEmail(function () use ($devis, $recapitulatif, $contexte): Email {
            return (new Email())
                ->from(new Address($this->fromEmail, 'Webble'))
                ->to(...$this->notificationEmails)
                ->replyTo(new Address((string) $devis->getEmail(), trim($devis->getPrenom().' '.$devis->getNom())))
                ->subject(sprintf('[Webble] Nouvelle demande de devis — %s', $recapitulatif['offre']))
                ->html($this->twig->render('emails/devis/admin.html.twig', $contexte))
                ->text($this->twig->render('emails/devis/admin.txt.twig', $contexte));
        }, 'nouvelle demande de devis');

        $this->envoyerEmail(function () use ($devis, $contexte): Email {
            return (new Email())
                ->from(new Address($this->fromEmail, 'Webble'))
                ->to(new Address((string) $devis->getEmail(), trim($devis->getPrenom().' '.$devis->getNom())))
                ->replyTo(...$this->notificationEmails)
                ->subject('Votre demande de devis a bien été reçue — Webble')
                ->html($this->twig->render('emails/devis/client.html.twig', $contexte))
                ->text($this->twig->render('emails/devis/client.txt.twig', $contexte));
        }, 'accusé de réception de devis');
    }

    public function envoyerNotificationFormation(DevisFormation $devisFormation): void
    {
        $formation = $devisFormation->getFormations();
        $prixHt = (float) ($formation?->getPrix() ?? 0);
        $totalTva = round($prixHt * AppConfig::TAUX_TVA, 2);
        $recapitulatif = [
            'reference' => sprintf('WEB-FOR-%s-%06d', date('Y'), (int) $devisFormation->getId()),
            'date' => new \DateTimeImmutable(),
            'formation' => $formation?->getLibelle() ?? 'Formation Webble',
            'date_souhaitee' => $devisFormation->getDateSouhaitee(),
            'duree' => $formation?->getDuree(),
            'prix_ht' => round($prixHt, 2),
            'total_tva' => $totalTva,
            'total_ttc' => round($prixHt + $totalTva, 2),
        ];
        $contexte = [
            'demande' => $devisFormation,
            'recapitulatif' => $recapitulatif,
            'logo_url' => 'https://webble.fr/images/webble_black.png',
            'contact_url' => 'https://webble.fr/contact',
        ];

        $this->envoyerEmail(function () use ($devisFormation, $recapitulatif, $contexte): Email {
            return (new Email())
                ->from(new Address($this->fromEmail, 'Webble'))
                ->to(...$this->notificationEmails)
                ->replyTo(new Address((string) $devisFormation->getEmail(), trim($devisFormation->getPrenom().' '.$devisFormation->getNom())))
                ->subject(sprintf('[Webble] Nouvelle demande de formation — %s', $recapitulatif['formation']))
                ->html($this->twig->render('emails/formation/admin.html.twig', $contexte))
                ->text($this->twig->render('emails/formation/admin.txt.twig', $contexte));
        }, 'nouvelle demande de formation');

        $this->envoyerEmail(function () use ($devisFormation, $contexte): Email {
            return (new Email())
                ->from(new Address($this->fromEmail, 'Webble'))
                ->to(new Address((string) $devisFormation->getEmail(), trim($devisFormation->getPrenom().' '.$devisFormation->getNom())))
                ->replyTo(...$this->notificationEmails)
                ->subject('Votre demande de formation a bien été reçue — Webble')
                ->html($this->twig->render('emails/formation/client.html.twig', $contexte))
                ->text($this->twig->render('emails/formation/client.txt.twig', $contexte));
        }, 'accusé de réception de formation');
    }

    private function envoyerEmail(callable $fabriqueEmail, string $contexte): void
    {
        try {
            $this->mailer->send($fabriqueEmail());
        } catch (\Throwable $exception) {
            $this->logger->error('Échec de l’envoi d’un e-mail : '.$contexte.'.', [
                'exception' => $exception,
            ]);
        }
    }
}

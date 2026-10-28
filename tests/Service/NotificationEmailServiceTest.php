<?php

namespace App\Tests\Service;

use App\Service\NotificationEmailService;
use PHPUnit\Framework\TestCase;
use Psr\Log\NullLogger;
use Symfony\Component\Mailer\MailerInterface;
use Symfony\Component\Mime\RawMessage;
use Twig\Environment;
use Twig\Loader\ArrayLoader;

class NotificationEmailServiceTest extends TestCase
{
    public function testUneNotificationAdministrativeEstEnvoyeeALAdresseConfiguree(): void
    {
        $mailer = $this->createMock(MailerInterface::class);
        $mailer->expects(self::once())
            ->method('send')
            ->with(self::callback(function (RawMessage $message): bool {
                $destinataires = array_map(static fn ($adresse) => $adresse->getAddress(), $message->getTo());

                self::assertSame(['stephane.mn@outlook.fr'], $destinataires);

                return true;
            }));

        $service = new NotificationEmailService(
            $mailer,
            new Environment(new ArrayLoader()),
            'postmaster@webble.fr',
            ['stephane.mn@outlook.fr'],
            new NullLogger()
        );

        $service->envoyerNotificationContact('<p>Message de test</p>');
    }
}

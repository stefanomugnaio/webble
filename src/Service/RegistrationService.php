<?php

namespace App\Service;

use App\Entity\Client;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;

class RegistrationService
{
    public function __construct(
        private EntityManagerInterface $em,
        private UserPasswordHasherInterface $hasher
    ) {}

    /**
     * Hash le mot de passe et enregistre le client.
     */
    public function enregistreClient(Client $client, string $motDePasseClair): void
    {
        // Hash du mot de passe
        $hash = $this->hasher->hashPassword($client, $motDePasseClair);
        $client->setPassword($hash);

        // Sauvegarde BDD
        $this->em->persist($client);
        $this->em->flush();
    }
}

<?php

namespace App\Service;

use DateTime;
use App\Entity\Contact;
use Doctrine\ORM\EntityManagerInterface;

class ContactService
{
    public function __construct(
        private EntityManagerInterface $em
    ) {}

    /**
     * Initialise les valeurs automatiques avant formulaire (si besoin)
     */
    public function initialize(Contact $contact): void
    {
        if (method_exists($contact, 'setDateCreation')) {
            $contact->setDateCreation(new DateTime());
        }
    }

    /**
     * Eregistrement du contact
     */
    public function save(Contact $contact): void
    {
        if (method_exists($contact, 'setDateCreation')) {
            $contact->setDateCreation(new DateTime());
        }

        $this->em->persist($contact);
        $this->em->flush();
    }
}

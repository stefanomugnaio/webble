<?php

namespace App\Service;

use App\Entity\Formation;
use App\Entity\DevisFormation;
use Doctrine\ORM\EntityManagerInterface;

class DevisFormationService
{
    public function __construct(
        private EntityManagerInterface $entityManager
    ) {}

    /**
     * Initialise un devis pour une formation
     */
    public function initialiserDevisPourFormation(Formation $formation): DevisFormation
    {
        $devis = new DevisFormation();

        $devis->setFormations($formation);
        $devis->setDevisFormation($formation->getLibelle());
        $devis->setDateDemande(new \DateTime());
        $devis->setRgpd(false);

        return $devis;
    }

    /**
     * Enregistre le devis en base
     */
    public function enregistrerDevisFormation(DevisFormation $devis): void
    {
        if (!$devis->getSessionFormation()) {
            throw new \LogicException(
                'Une session doit être sélectionnée.'
            );
        }

        if (!$devis->isRgpd()) {
            throw new \LogicException(
                'Le RGPD doit être accepté.'
            );
        }

        $this->entityManager->persist($devis);
        $this->entityManager->flush();
    }
}

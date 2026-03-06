<?php

namespace App\Service;

use App\Entity\Formation;
use App\Entity\SessionFormation;
use Doctrine\ORM\EntityManagerInterface;

class SessionFormationService
{
    public function __construct(
        private EntityManagerInterface $entityManager
    ) {}

    /**
     * Récupère toutes les sessions d'une formation
     */
    public function recupererSessionsPourFormation(Formation $formation): array
    {
        return $this->entityManager
            ->getRepository(SessionFormation::class)
            ->findBy(['Formation' => $formation]);
    }

    /**
     * Récupère uniquement les sessions disponibles
     */
    public function recupererSessionsDisponiblesPourFormation(Formation $formation): array
    {
        return $this->entityManager
            ->getRepository(SessionFormation::class)
            ->findBy([
                'Formation' => $formation,
                'status' => 'disponible'
            ]);
    }

    /**
     * Création d'une session
     */
    public function creerSession(SessionFormation $session): void
    {
        if ($session->getDateFin() < $session->getDateDebut()) {
            throw new \LogicException(
                'La date de fin ne peut pas être antérieure à la date de début.'
            );
        }

        if (!$session->getStatus()) {
            $session->setStatus('disponible');
        }

        $this->entityManager->persist($session);
        $this->entityManager->flush();
    }

    public function recupererToutesLesSessions(){
        return $this->entityManager
            ->getRepository(SessionFormation::class)
            ->findAll();
    }
}

<?php

namespace App\Service;

use App\Entity\Formation;
use Doctrine\ORM\EntityManagerInterface;

class FormationService
{
    public function __construct(
        private EntityManagerInterface $entityManager
    ) {}

    public function recupererFormationParId(int $id): ?Formation
    {
        return $this->entityManager
            ->getRepository(Formation::class)
            ->find($id);
    }

    public function recupererFormationParSlug(string $slug): ?Formation
    {
        return $this->entityManager
            ->getRepository(Formation::class)
            ->findOneBy([
                'slug' => $slug
            ]);
    }

    public function recupererToutesLesFormations(): array
    {
        return $this->entityManager
            ->getRepository(Formation::class)
            ->findAll();
    }

    public function enregistrerFormation(Formation $formation): void
    {
        $this->entityManager->persist($formation);
        $this->entityManager->flush();
    }
}

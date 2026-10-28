<?php

namespace App\Repository;

use App\Entity\DisponibiliteFormation;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @extends ServiceEntityRepository<DisponibiliteFormation>
 */
class DisponibiliteFormationRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, DisponibiliteFormation::class);
    }

    /**
     * @return DisponibiliteFormation[]
     */
    public function findUpcoming(): array
    {
        return $this->createQueryBuilder('disponibilite')
            ->andWhere('disponibilite.date >= :today')
            ->setParameter('today', new \DateTimeImmutable('today'))
            ->orderBy('disponibilite.date', 'ASC')
            ->getQuery()
            ->getResult();
    }
}

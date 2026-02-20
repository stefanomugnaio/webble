<?php

namespace App\Service;

use App\Entity\DevisFormation;
use App\Entity\Formation;
use App\Repository\FormationRepository;
use App\Repository\SessionFormationRepository;
use DateTime;
use Doctrine\ORM\EntityManagerInterface;
use InvalidArgumentException;

class EnvoiDevisFormationService
{
    public function __construct(
        private EntityManagerInterface $entityManager,
        private FormationRepository $formationRepository,
        private SessionFormationRepository $sessionRepository
    ) {}

    /**
     * Récupération des données de formation
     */
    public function recupererDonneesFormation(string $slug): Formation
    {
        $formation = $this->formationRepository->findOneBy([
            'slug' => $slug
        ]);

        if (!$formation) {
            throw new InvalidArgumentException('Formation inconnue.');
        }

        return $formation;
    }

    /**
     * Récupération des sessions disponibles
     */
    public function recupererSessionsDisponibles(): array
    {
        return $this->sessionRepository->findBy([
            'status' => 'disponible'
        ], [
            'date_debut' => 'ASC'
        ]);
    }

    /**
     * Calcul des montants
     */
    public function calculerMontants(Formation $formation, float $tauxTva): array
    {
        $prixHt = $formation->getPrix();
        $tva = $prixHt * $tauxTva;

        return [
            'prix_ht' => $prixHt,
            'total_tva' => $tva,
            'total_ttc' => $prixHt + $tva,
        ];
    }

    /**
     * Enregistrement de la demande de formation
     */
    public function enregistrerDemande(
        DevisFormation $devisFormation,
        string $libelleFormation
    ): bool
    {
        // Sécurité si jamais formation non définie
        if (!$devisFormation->getFormations()) {
            $formation = $this->formationRepository->findOneBy([
                'libelle' => $libelleFormation
            ]);

            if ($formation) {
                $devisFormation->setFormations($formation);
            }
        }

        $devisFormation->setDateDemande(new DateTime());

        $this->entityManager->persist($devisFormation);
        $this->entityManager->flush();

        return true;
    }
}
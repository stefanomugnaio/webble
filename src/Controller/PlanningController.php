<?php

namespace App\Controller;

use App\Entity\SessionFormation;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;

class PlanningController extends AbstractController
{
    #[Route('/admin/session-formation/{id}/planning', name: 'admin_session_formation_planning', methods: ['GET'])]
    public function planning(SessionFormation $sessionFormation): Response
    {
        $this->denyAccessUnlessGranted('ROLE_ADMIN');

        /**
         * IMPORTANT :
         * ------------------------------------------------------
         * Pour faire un vrai planning, il faut idéalement une entité
         * de créneaux liée à SessionFormation.
         *
         * Exemple futur :
         * $sessionFormation->getCreneaux()
         *
         * Comme tu ne m’as pas encore donné cette entité,
         * je prépare ici un tableau vide + exemple de structure.
         */

        $creneaux = [];

        /**
         * EXEMPLE DE CE QU’ON FERA PLUS TARD :
         *
         * foreach ($sessionFormation->getCreneaux() as $creneau) {
         *     $dateDebut = $creneau->getDateDebut();
         *     $dateFin = $creneau->getDateFin();
         *
         *     if (!$dateDebut || !$dateFin) {
         *         continue;
         *     }
         *
         *     $creneaux[] = [
         *         'id'         => $creneau->getId(),
         *         'jour'       => (int) $dateDebut->format('N'), // 1 = lundi, 7 = dimanche
         *         'date'       => $dateDebut,
         *         'heureDebut' => $dateDebut->format('H:i'),
         *         'heureFin'   => $dateFin->format('H:i'),
         *         'titre'      => $sessionFormation->getFormation()?->getLibelle() ?? 'Formation',
         *         'couleur'    => $this->getCouleurByStatus($sessionFormation->getStatus()),
         *     ];
         * }
         */

        /**
         * TEMPORAIRE :
         * ------------------------------------------------------
         * On génère quelques blocs fictifs si tu veux tester le rendu
         * avant d’avoir créé l’entité des créneaux.
         */
        if (empty($creneaux)) {
            $titre = $sessionFormation->getFormation()?->getLibelle()
                ?? $sessionFormation->getFormation()?->getLibelle()
                ?? 'Formation';

            $creneaux = [
                [
                    'id'         => 1,
                    'jour'       => 1,
                    'date'       => new \DateTime('next monday'),
                    'heureDebut' => '18:00',
                    'heureFin'   => '20:00',
                    'titre'      => $titre . ' - Cours 1',
                    'couleur'    => $this->getCouleurByStatus($sessionFormation->getStatus()),
                ],
                [
                    'id'         => 2,
                    'jour'       => 3,
                    'date'       => new \DateTime('next wednesday'),
                    'heureDebut' => '18:00',
                    'heureFin'   => '20:00',
                    'titre'      => $titre . ' - Cours 2',
                    'couleur'    => $this->getCouleurByStatus($sessionFormation->getStatus()),
                ],
                [
                    'id'         => 3,
                    'jour'       => 6,
                    'date'       => new \DateTime('next saturday'),
                    'heureDebut' => '09:00',
                    'heureFin'   => '13:00',
                    'titre'      => $titre . ' - Atelier',
                    'couleur'    => $this->getCouleurByStatus($sessionFormation->getStatus()),
                ],
            ];
        }

        return $this->render('session_formation/planning.html.twig', [
            'sessionFormation' => $sessionFormation,
            'creneaux' => $creneaux,
        ]);
    }

    private function getCouleurByStatus(?string $status): string
    {
        $status = mb_strtolower((string) $status);

        return match ($status) {
            'disponible', 'ouverte', 'open' => '#198754',
            'complet', 'complète', 'complete' => '#ffc107',
            'annulee', 'annulée', 'annule', 'cancelled' => '#dc3545',
            default => '#0d6efd',
        };
    }
}
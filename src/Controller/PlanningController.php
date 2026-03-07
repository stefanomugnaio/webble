<?php

namespace App\Controller;

use App\Repository\SessionFormationRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;

class PlanningController extends AbstractController
{
    #[Route('/planning', name: 'app_planning', methods: ['GET'])]
    public function planning(): Response
    {
        return $this->render('planning/index.html.twig');
    }

    #[Route('/planning/evenements', name: 'planning_formations_evenements', methods: ['GET'])]
    public function planningEvents(SessionFormationRepository $sessionFormationRepository): JsonResponse
    {
        $sessions = $sessionFormationRepository->findAll();

        $events = [];

        foreach ($sessions as $session) {
            $formation = $session->getFormation();

            $libelle = $formation?->getLibelle() ?? 'Formation';

            $dateDebut = $session->getDateDebut();
            $dateFin = $session->getDateFin();

            if (!$dateDebut || !$dateFin) {
                continue;
            }

            $couleur = match (mb_strtolower((string) $session->getStatus())) {
                'disponible', 'ouverte', 'open' => '#198754',
                'complet', 'complète', 'complete' => '#ffc107',
                'annulee', 'annulée', 'annule', 'cancelled' => '#dc3545',
                default => '#0d6efd',
            };

            $bloc = $session->getBloc();
            $heureDebut = $session->getHeureDebut();
            $heureFin = $session->getHeureFin();

            $title = $libelle;

            if ($bloc !== null) {
                $title .= ' - Bloc ' . $bloc;
            }

            if ($heureDebut && $heureFin) {
                $title .= ' - ' . $heureDebut->format('H:i') . ' à ' . $heureFin->format('H:i');
            }

            $events[] = [
                'id' => $session->getId(),
                'title' => $title,
                'start' => $dateDebut->format('Y-m-d'),
                'end' => (clone $dateFin)->modify('+1 day')->format('Y-m-d'),
                'backgroundColor' => $couleur,
                'borderColor' => $couleur,
                'textColor' => '#ffffff',
                'extendedProps' => [
                    'status' => $session->getStatus(),
                    'duree' => $session->getDuree(),
                    'bloc' => $bloc,
                    'heureDebut' => $heureDebut ? $heureDebut->format('H:i') : null,
                    'heureFin' => $heureFin ? $heureFin->format('H:i') : null,
                ],
            ];
        }

        return $this->json($events);
    }
}
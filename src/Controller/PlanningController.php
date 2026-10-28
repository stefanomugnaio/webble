<?php

namespace App\Controller;

use App\Entity\DisponibiliteFormation;
use App\Repository\DisponibiliteFormationRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Component\Security\Http\Attribute\IsGranted;

class PlanningController extends AbstractController
{
    #[Route('/planning', name: 'app_planning', methods: ['GET'])]
    public function planning(): Response
    {
        return $this->render('planning/index.html.twig');
    }

    #[Route('/planning/evenements', name: 'planning_formations_evenements', methods: ['GET'])]
    public function planningEvents(DisponibiliteFormationRepository $repository): JsonResponse
    {
        return $this->json(array_map(
            static fn (DisponibiliteFormation $disponibilite): array => [
                'id' => $disponibilite->getId(),
                'title' => 'Disponible',
                'start' => $disponibilite->getDate()?->format('Y-m-d'),
                'allDay' => true,
                'backgroundColor' => '#198754',
                'borderColor' => '#198754',
                'textColor' => '#ffffff',
            ],
            $repository->findUpcoming()
        ));
    }

    #[Route('/admin/planning', name: 'admin_planning', methods: ['GET'])]
    #[IsGranted('ROLE_ADMIN')]
    public function adminPlanning(): Response
    {
        return $this->render('admin/planning.html.twig');
    }

    #[Route('/admin/planning/disponibilites/basculer', name: 'admin_planning_toggle', methods: ['POST'])]
    #[IsGranted('ROLE_ADMIN')]
    public function toggleAvailability(
        Request $request,
        DisponibiliteFormationRepository $repository,
        EntityManagerInterface $entityManager
    ): JsonResponse {
        try {
            $payload = $request->toArray();
        } catch (\JsonException) {
            return $this->json(['message' => 'Requête invalide.'], Response::HTTP_BAD_REQUEST);
        }

        if (!$this->isCsrfTokenValid('admin_planning_toggle', (string) ($payload['_token'] ?? ''))) {
            return $this->json(['message' => 'Jeton de sécurité invalide.'], Response::HTTP_FORBIDDEN);
        }

        $dateValue = (string) ($payload['date'] ?? '');
        $date = \DateTimeImmutable::createFromFormat('!Y-m-d', $dateValue);

        if (!$date || $date->format('Y-m-d') !== $dateValue || $date < new \DateTimeImmutable('today')) {
            return $this->json(['message' => 'Choisissez une journée à venir.'], Response::HTTP_UNPROCESSABLE_ENTITY);
        }

        $availability = $repository->findOneBy(['date' => $date]);

        if ($availability) {
            $entityManager->remove($availability);
            $available = false;
        } else {
            $entityManager->persist((new DisponibiliteFormation())->setDate($date));
            $available = true;
        }

        $entityManager->flush();

        return $this->json([
            'date' => $dateValue,
            'available' => $available,
            'message' => $available ? 'Journée marquée comme disponible.' : 'Disponibilité retirée.',
        ]);
    }
}

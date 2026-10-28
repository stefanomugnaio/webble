<?php

namespace App\Controller;

use App\Entity\TacheOffre;
use App\Form\AdminTacheOffreType;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Component\Security\Http\Attribute\IsGranted;

#[Route('/admin/taches-offres', name: 'admin_tache_offre_')]
#[IsGranted('ROLE_ADMIN')]
class AdminTacheOffreController extends AbstractController
{
    #[Route('', name: 'index', methods: ['GET'])]
    public function index(EntityManagerInterface $em): Response
    {
        return $this->render('admin/tache_offre/index.html.twig', ['taches' => $em->getRepository(TacheOffre::class)->findBy([], ['libelle' => 'ASC'])]);
    }

    #[Route('/nouvelle', name: 'new', methods: ['GET', 'POST'])]
    #[Route('/{id}/modifier', name: 'edit', methods: ['GET', 'POST'], requirements: ['id' => '\\d+'])]
    public function form(Request $request, EntityManagerInterface $em, ?TacheOffre $tache = null): Response
    {
        $tache ??= new TacheOffre();
        $isNew = null === $tache->getId();
        $form = $this->createForm(AdminTacheOffreType::class, $tache)->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $em->persist($tache);
            $em->flush();
            $this->addFlash('success', $isNew ? 'Tâche ajoutée à la bibliothèque.' : 'Tâche mise à jour.');
            return $this->redirectToRoute('admin_tache_offre_index');
        }

        return $this->render('admin/form.html.twig', ['form' => $form, 'title' => $isNew ? 'Ajouter une tâche' : 'Modifier la tâche', 'resource' => 'tache_offre']);
    }

    #[Route('/{id}', name: 'delete', methods: ['POST'], requirements: ['id' => '\\d+'])]
    public function delete(TacheOffre $tache, Request $request, EntityManagerInterface $em): Response
    {
        if ($this->isCsrfTokenValid('delete_tache_offre'.$tache->getId(), $request->request->getString('_token'))) {
            $em->remove($tache);
            $em->flush();
            $this->addFlash('success', 'Tâche supprimée de la bibliothèque. Les offres existantes restent inchangées.');
        }
        return $this->redirectToRoute('admin_tache_offre_index');
    }
}

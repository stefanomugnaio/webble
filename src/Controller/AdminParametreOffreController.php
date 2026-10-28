<?php

namespace App\Controller;

use App\Entity\ParametreOffre;
use App\Form\AdminParametreOffreType;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Component\Security\Http\Attribute\IsGranted;

#[Route('/admin/parametres-offres', name: 'admin_parametre_offre_')]
#[IsGranted('ROLE_ADMIN')]
class AdminParametreOffreController extends AbstractController
{
    #[Route('', name: 'edit', methods: ['GET', 'POST'])]
    public function edit(Request $request, EntityManagerInterface $em): Response
    {
        $parametre = $em->getRepository(ParametreOffre::class)->findOneBy([]) ?? new ParametreOffre();
        $form = $this->createForm(AdminParametreOffreType::class, $parametre)->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $em->persist($parametre);
            $em->flush();
            $this->addFlash('success', 'Papier à en-tête enregistré. Il sera proposé pour les prochaines offres.');
            return $this->redirectToRoute('admin_parametre_offre_edit');
        }

        return $this->render('admin/parametre_offre/edit.html.twig', ['form' => $form, 'parametre' => $parametre]);
    }
}

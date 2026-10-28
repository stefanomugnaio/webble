<?php

namespace App\Controller;

use App\Entity\LigneOffre;
use App\Entity\OffreCommerciale;
use App\Entity\ParametreOffre;
use App\Entity\TacheOffre;
use App\Form\AdminOffreCommercialeType;
use App\Service\OffrePdfService;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Form\FormError;
use Symfony\Component\HttpFoundation\HeaderUtils;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Component\Security\Http\Attribute\IsGranted;

#[Route('/admin/offres-commerciales', name: 'admin_offre_')]
#[IsGranted('ROLE_ADMIN')]
class AdminOffreCommercialeController extends AbstractController
{
    #[Route('', name: 'index', methods: ['GET'])]
    public function index(EntityManagerInterface $em): Response
    {
        return $this->render('admin/offre/index.html.twig', ['offres' => $em->getRepository(OffreCommerciale::class)->findBy([], ['dateEmission' => 'DESC', 'id' => 'DESC'])]);
    }

    #[Route('/nouvelle', name: 'new', methods: ['GET', 'POST'])]
    #[Route('/{id}/modifier', name: 'edit', methods: ['GET', 'POST'], requirements: ['id' => '\\d+'])]
    public function form(Request $request, EntityManagerInterface $em, ?OffreCommerciale $offre = null): Response
    {
        $offre ??= new OffreCommerciale();
        $isNew = null === $offre->getId();

        if ($isNew) {
            $offre->setNumero($this->genererNumero());
            $parametre = $em->getRepository(ParametreOffre::class)->findOneBy([]);
            $offre->setPapierEnteteUrl($parametre?->getPapierEnteteUrl());
        }

        $form = $this->createForm(AdminOffreCommercialeType::class, $offre)->handleRequest($request);
        if ($form->isSubmitted() && $offre->getLignes()->isEmpty()) {
            $form->get('lignes')->addError(new FormError('Ajoutez au moins une ligne à l’offre.'));
        }

        if ($form->isSubmitted() && $form->isValid()) {
            foreach ($offre->getLignes() as $position => $ligne) {
                $ligne->setPosition($position);
            }
            $em->persist($offre);
            $em->flush();
            $this->addFlash('success', $isNew ? 'Offre créée. Vous pouvez maintenant télécharger son PDF.' : 'Offre mise à jour.');
            return $this->redirectToRoute('admin_offre_show', ['id' => $offre->getId()]);
        }

        return $this->render('admin/offre/form.html.twig', [
            'form' => $form,
            'offre' => $offre,
            'is_new' => $isNew,
            'taches' => $em->getRepository(TacheOffre::class)->findBy([], ['libelle' => 'ASC']),
        ]);
    }

    #[Route('/{id}', name: 'show', methods: ['GET'], requirements: ['id' => '\\d+'])]
    public function show(OffreCommerciale $offre): Response
    {
        return $this->render('admin/offre/show.html.twig', ['offre' => $offre]);
    }

    #[Route('/{id}/pdf', name: 'pdf', methods: ['GET'], requirements: ['id' => '\\d+'])]
    public function pdf(OffreCommerciale $offre, OffrePdfService $pdfService): Response
    {
        try {
            $contenu = $pdfService->generer($offre);
        } catch (\Throwable) {
            $this->addFlash('danger', 'Le PDF n’a pas pu être généré. Vérifiez notamment le lien du papier à en-tête.');
            return $this->redirectToRoute('admin_offre_show', ['id' => $offre->getId()]);
        }

        $nom = preg_replace('/[^a-zA-Z0-9_-]+/', '-', $offre->getNumero()) ?: 'offre-webble';
        return new Response($contenu, Response::HTTP_OK, [
            'Content-Type' => 'application/pdf',
            'Content-Disposition' => HeaderUtils::makeDisposition(HeaderUtils::DISPOSITION_ATTACHMENT, $nom.'.pdf'),
        ]);
    }

    #[Route('/{id}/dupliquer', name: 'duplicate', methods: ['POST'], requirements: ['id' => '\\d+'])]
    public function duplicate(OffreCommerciale $offre, Request $request, EntityManagerInterface $em): Response
    {
        if (!$this->isCsrfTokenValid('duplicate_offre'.$offre->getId(), $request->request->getString('_token'))) {
            return $this->redirectToRoute('admin_offre_index');
        }

        $copie = (new OffreCommerciale())
            ->setNumero($this->genererNumero())
            ->setStatut('brouillon')
            ->setClientSociete($offre->getClientSociete())
            ->setClientNom($offre->getClientNom())
            ->setClientEmail($offre->getClientEmail())
            ->setClientAdresse($offre->getClientAdresse())
            ->setObjet($offre->getObjet())
            ->setIntroduction($offre->getIntroduction())
            ->setConditions($offre->getConditions())
            ->setTauxTva($offre->getTauxTva())
            ->setPapierEnteteUrl($offre->getPapierEnteteUrl());

        foreach ($offre->getLignes() as $ligne) {
            $copie->addLigne((new LigneOffre())->setLibelle($ligne->getLibelle())->setQuantite($ligne->getQuantite())->setPrixUnitaire($ligne->getPrixUnitaire())->setOfferte($ligne->isOfferte())->setPosition($ligne->getPosition()));
        }
        $em->persist($copie);
        $em->flush();
        $this->addFlash('success', 'Offre dupliquée en brouillon.');
        return $this->redirectToRoute('admin_offre_edit', ['id' => $copie->getId()]);
    }

    #[Route('/{id}', name: 'delete', methods: ['POST'], requirements: ['id' => '\\d+'])]
    public function delete(OffreCommerciale $offre, Request $request, EntityManagerInterface $em): Response
    {
        if ($this->isCsrfTokenValid('delete_offre'.$offre->getId(), $request->request->getString('_token'))) {
            $em->remove($offre);
            $em->flush();
            $this->addFlash('success', 'Offre supprimée.');
        }
        return $this->redirectToRoute('admin_offre_index');
    }

    private function genererNumero(): string
    {
        return sprintf('OFF-%s-%s', date('Ymd-His'), strtoupper(substr(bin2hex(random_bytes(2)), 0, 4)));
    }
}

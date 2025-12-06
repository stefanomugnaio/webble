<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;

final class CreationSiteController extends AbstractController
{
    #[Route('/creation-site', name: 'app_creation_site')]
    public function index(): Response
    {
        return $this->render('creation_site/index.html.twig', [
            'controller_name' => 'CreationSiteController',
        ]);
    }
}

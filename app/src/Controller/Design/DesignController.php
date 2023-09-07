<?php

namespace App\Controller\Design;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class DesignController extends AbstractController
{
    #[Route('/design/design', name: 'app_design_design')]
    public function index(): Response
    {
        return $this->render('design/application_document.html.twig', [
            'controller_name' => 'DesignController',
        ]);
    }
}

<?php

namespace App\Controller\Invocies;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class InvociesController extends AbstractController
{
    #[Route('/invocies/invocies', name: 'app_invocies_invocies')]
    public function index(): Response
    {
        return $this->render('invocies/index.html.twig', [
            'controller_name' => 'InvociesController',
        ]);
    }
}
